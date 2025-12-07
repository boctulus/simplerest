<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Exceptions\SchemaException;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\ExceptionHandler;
use Boctulus\Simplerest\Core\Traits\InsertWithSubResourcesTrait;
use Boctulus\Simplerest\Core\Traits\RelationshipTrait;
use Boctulus\Simplerest\Core\Traits\QueryBuilderTrait;
use Boctulus\Simplerest\Core\Traits\SubResourceHandler;

class Model
{
	use ExceptionHandler;
	use QueryBuilderTrait;
	use SubResourceHandler;
	use RelationshipTrait;
	use InsertWithSubResourcesTrait;

	public    $exec = true;
	protected $schema;

	// ORM properties
	protected static $table;
	protected $orm_attributes = [];
	protected $exists = false;
	protected $original = [];

	// Hidratation
	static function hydratate($instance, array $attributes)
	{
		// Set ORM attributes (the actual data)
		$instance->orm_attributes = $attributes;

		// Mark as existing record
		$instance->exists = true;

		// Store original state for dirty checking
		$instance->original = $attributes;

		return $instance;
	}

	static function findOrFail($id)
	{
		$instance = new static(true); // true = connect to DB

		// Use find() to set up the where clause and get data
		$data = $instance->find($id)->first();

		// Check if record exists
		if (!$data || empty($data)) {
			throw new \Exception("Resource for `{$instance->table_name}` and id={$id} doesn't exist");
		}

		// "Hidratacion"
		return static::hydratate($instance, $data);
	}

	function __construct(bool $connect = false, $schema = null, bool $load_config = true)
	{
		$this->exec = true;
		$this->boot();

		$this->prefix = DB::getTablePrefix();

		// static::$sql_formatter_callback = function(string $sql, bool $highlight = false){
		// 	return \SqlFormatter::format($sql, $highlight);
		// };

		if ($connect){
			$this->connect();
		}

		if ($schema != null){
			$this->schema = $schema::get(); //
			$this->table_name = $this->schema['table_name'];
		}

		if ($load_config){
			$this->config = Config::get();

			if ($this->config['error_handling']) {
				set_exception_handler([$this, 'exception_handler']);
			}
		}
		
		if ($this->schema == null){
			return;
		}	

		$this->attributes = array_keys($this->schema['attr_types']);

		if (in_array('', $this->attributes, true)){
			throw new SchemaException("Invalid attribute");
		}

		if ($this->fillable == null){
			$this->fillable = $this->attributes;
		}

		$this->schema['nullable'][] = $this->is_locked;		
		$this->schema['nullable'][] = $this->createdAt;
		$this->schema['nullable'][] = $this->updatedAt;
		$this->schema['nullable'][] = $this->deletedAt;
		$this->schema['nullable'][] = $this->createdBy;
		/*
			No incluir:

			$this->schema['nullable'][] = $this->updatedBy;

		*/
		$this->schema['nullable'][] = $this->deletedBy;
		$this->schema['nullable'][] = $this->belongsTo;	

		$to_fill = [];

		$this->fill($to_fill);	
		
		$this->soft_delete = $this->inSchema([$this->deletedAt]);

		// Kill dupes
		$this->schema['nullable'] = array_unique($this->schema['nullable']);
	
		/*
		 Validations
		*/
		if (!empty($this->schema['rules'])){
			foreach ($this->schema['rules'] as $field => $rule){
				if (!isset($this->schema['rules'][$field]['type']) || empty($this->schema['rules'][$field]['type'])){
					$this->schema['rules'][$field]['type'] = strtolower($this->schema['attr_types'][$field]);
				}
			}
		}
		
		foreach ($this->schema['attr_types'] as $field => $type){
			if (!isset($this->schema['rules'][$field])){
				$this->schema['rules'][$field]['type'] = strtolower($type);
			}

			if (!$this->isNullable($field)){
				$this->schema['rules'][$field]['required'] = true;
			}
		}

		//dd($this->not_fillable, 'NF');

		/*
			Remuevo los campos no-fillables de los fillables
		*/
		foreach ($this->not_fillable as $f){
			$pos = array_search($f, $this->fillable);
			
			if ($pos !== false){
				unset($this->fillable[$pos]);
			}
		}

		//$this->setValidator(new Validator());

		// event handler
		$this->init();
	}

	/*
		Even hooks -podrían estar definidos en clase abstracta o interfaz-
	*/

	protected function boot() { }

	protected function onReading() { }
	protected function onRead(int $count) { }

	protected function onCreating(Array &$data) {	}
	protected function onCreated(Array &$data, $last_inserted_id) { }

	protected function onUpdating(Array &$data) { }
	protected function onUpdated(Array &$data, ?int $count) { }

	protected function onDeleting(Array &$data) { }
	protected function onDeleted(Array &$data, ?int $count) { }

	protected function onRestoring(Array &$data) { }
	protected function onRestored(Array &$data, ?int $count) { }

	protected function init() { }

	/**
	 * Set the value of conn
	 *
	 * @return  self
	 */ 
	function connect()
	{
		$this->conn = DB::getConnection();
		return $this;
	}

	function setConn($conn)
	{
		$this->conn = $conn;
		return $this;
	}

	function getConn(){
		return $this->conn;
	}

	/*
		ORM Methods - Laravel-like Active Record pattern

		https://chatgpt.com/c/68f49b1c-7170-8321-8c87-7351564630d2
	*/

	/**
	 * Static query() method - returns a new query builder instance
	 * Usage: Model::query()->where(...)->get()
	 *
	 * @throws \BadMethodCallException if static::$table is not defined
	 */
	static function query()
	{
		if (!isset(static::$table)) {
			throw new \BadMethodCallException(
				"Static method query() requires static::\$table to be defined in " . static::class
			);
		}

		return new static(true);
	}

	/**
	 * Static all() method - get all records
	 *
	 * @throws \BadMethodCallException if static::$table is not defined
	 */
	static function all()
	{
		if (!isset(static::$table)) {
			throw new \BadMethodCallException(
				"Static method all() requires static::\$table to be defined in " . static::class
			);
		}

		$instance = new static(true);
		return $instance->get();
	}

	/**
	 * Check if the model instance exists in the database
	 */
	function exists()
	{
		return $this->exists;
	}

	/**
	 * Get an ORM attribute value
	 */
	function __get($key)
	{
		if (array_key_exists($key, $this->orm_attributes)) {
			return $this->orm_attributes[$key];
		}
		return null;
	}

	/**
	 * Set an ORM attribute value
	 */
	function __set($key, $value)
	{
		$this->orm_attributes[$key] = $value;
	}

	/**
	 * Check if an ORM attribute is set
	 */
	function __isset($key)
	{
		return isset($this->orm_attributes[$key]);
	}

}
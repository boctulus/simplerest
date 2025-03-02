<?php

namespace simplerest\core;

use simplerest\core\exceptions\SchemaException;
use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\traits\ExceptionHandler;
use simplerest\core\traits\InsertWithSubResourcesTrait;
// use simplerest\core\traits\RelationshipTrait;
use simplerest\core\traits\QueryBuilderTrait;
use simplerest\core\traits\SubResourceHandler;

class Model 
{	
	use ExceptionHandler;
	use QueryBuilderTrait;
	use SubResourceHandler;	
	// use RelationshipTrait;
	use InsertWithSubResourcesTrait;
	
	public    $exec = true;
	protected $schema;
	

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

}
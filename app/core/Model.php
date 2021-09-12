<?php

namespace simplerest\core;

use simplerest\libs\DB;
use simplerest\libs\Arrays;
use simplerest\libs\Strings;
use simplerest\libs\Validator;
use simplerest\libs\ValidationRules;
use simplerest\libs\Factory;
use simplerest\libs\Debug;
use simplerest\core\interfaces\IValidator;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\core\exceptions\SqlException;
use simplerest\core\interfaces\ITransformer;
use simplerest\traits\ExceptionHandler;


class Model {
	use ExceptionHandler;

	// for internal use
	protected $table_alias = '';
	protected $table_name;

	// Schema
	protected $schema;

	protected $fillable = [];
	protected $not_fillable = [];
	protected $hidden   = [];
	protected $attributes = [];
	protected $joins  = [];
	protected $show_deleted = false;
	protected $conn;
	protected $fields = [];
	protected $where  = [];
	protected $where_group_op  = [];
	protected $where_having_op = [];
	protected $group  = [];
	protected $having = [];
	protected $w_vars = [];
	protected $h_vars = [];
	protected $w_vals = [];
	protected $h_vals = [];
	protected $order  = [];
	protected $raw_order = [];
	protected $select_raw_q;
	protected $select_raw_vals = [];
	protected $where_raw_q;
	protected $where_raw_vals  = [];
	protected $having_raw_q;
	protected $having_raw_vals = [];
	protected $table_raw_q;
	protected $from_raw_vals   = [];
	protected $union_q;
	protected $union_vals = [];
	protected $union_type;
	protected $randomize = false;
	protected $distinct  = false;
	protected $to_merge_bindings = [];
	protected $last_pre_compiled_query;
	protected $last_bindings = [];
	protected $limit;
	protected $offset;
	protected $pag_vals = [];
	protected $validator;
	protected $input_mutators = [];
	protected $output_mutators = [];
	protected $transformer;
	protected $controller;
	protected $exec = true;
	protected $fetch_mode;
	protected $soft_delete;
	protected $last_inserted_id;
	protected $paginator = true;
	protected $fetch_mode_default = \PDO::FETCH_ASSOC;
	protected $data = []; 

	protected $createdAt = 'created_at';
	protected $updatedAt = 'updated_at';
	protected $deletedAt = 'deleted_at'; 
	protected $createdBy = 'created_by';
	protected $updatedBy = 'updated_by';
	protected $deletedBy = 'deleted_by'; 
	protected $locked    = 'locked';
	protected $belongsTo = 'belongs_to';

	
	function createdAt(){
		return $this->createdBy;
	}

	function createdBy(){
		return $this->createdBy;
	}

	function updatedAt(){
		return $this->updatedAt;
	}

	function updatedBy(){
		return $this->updatedBy;
	}

	function deletedAt(){
		return $this->deletedAt;
	}

	function deletedBy(){
		return $this->deletedBy;
	}

	function locked(){
		return $this->locked;
	}

	function belongsTo(){
		return $this->belongsTo;
	}


	function __construct(bool $connect = false, $schema = null){
		if ($connect){
			$this->connect();
		}

		if ($schema != null){
			$this->schema = $schema->get();
			$this->table_name = $this->schema['table_name'];
		}

		$this->config = Factory::config();

		if ($this->config['error_handling']) {
            set_exception_handler([$this, 'exception_handler']);
		}
		
		/////////////// ***
		/*
		if (empty($this->table_name)){
			$class_name = get_class($this);
			$class_name = substr($class_name, strrpos($class_name, '\\')+1);
			$str = Strings::camelToSnake($class_name);
			$this->table_name = strtolower(substr($str, 0, strlen($str)-6));
		}
		*/
	

		//dd($this->table_name, 'table_name:');  // mode <-- por "model" recortado!!
		
		if ($this->schema == null){
			return;
		}	

		//dd($this->schema, 'SCHEMA:');

		$this->attributes = array_keys($this->schema['attr_types']);

		if (in_array('', $this->attributes, true)){
			throw new \Exception("An attribute is invalid");
		}
		
		/*
		if ($this->schema['id_name'] == NULL){
			if ($this->inSchema(['id'])){
				$this->schema['id_name'] = 'id';
			} else {
				throw new \Exception("Undefined table identifier for '".$this->table_name. "' Use 'id' or \$id_name to specify another field name");
			}
		}			
		*/

	
		if ($this->fillable == NULL){
			$this->fillable = $this->attributes;
			$this->unfill([
							$this->locked, 
							$this->createdAt,							
							$this->updatedAt, 							
							$this->deletedAt, 
							$this->createdBy, 
							$this->updatedBy, 
							$this->deletedBy
			]);	
		}

		$this->unfill($this->not_fillable);

		// debería ser innecesario pues debería provenir del propio schema !
		$this->schema['nullable'][] = $this->locked;		
		$this->schema['nullable'][] = $this->createdAt;
		$this->schema['nullable'][] = $this->updatedAt;
		$this->schema['nullable'][] = $this->deletedAt;
		$this->schema['nullable'][] = $this->createdBy;
		$this->schema['nullable'][] = $this->updatedBy;
		$this->schema['nullable'][] = $this->deletedBy;
		$this->schema['nullable'][] = $this->belongsTo;

		$to_fill = [];

		if (!empty($this->schema['id_name'])){
			$to_fill[] = $this->schema['id_name'];
		}

		if ($this->inSchema([$this->createdBy])){
			$to_fill[] = $this->createdBy;
		}

		if ($this->inSchema([$this->updatedBy])){
			$to_fill[] = $this->updatedBy;
		}

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
		
		// event handler
		$this->boot();
	}


	function addRules(ValidationRules $vr){
		$this->schema['rules'] = array_merge($this->schema['rules'], $vr->getRules());
	}

	/*
		Returns prmary key
	*/
	function getKeyName(){
		return $this->schema['id_name'];
	}

	function getTableName(){
		return $this->table_name;
	}

	/*
		Turns on / off pagination
	*/
	function setPaginator(bool $status){
		$this->paginator = $status;
		return $this;
	}


	function registerInputMutator(string $field, callable $fn, ?callable $apply_if_fn){
		$this->input_mutators[$field] = [$fn, $apply_if_fn];
		return $this;
	}

	function registerOutputMutator(string $field, callable $fn){
		$this->output_mutators[$field] = $fn;
		return $this;
	}

	// acepta un Transformer
	function registerTransformer(ITransformer $t, $controller = NULL){
		$this->unhideAll();
		$this->transformer = $t;
		$this->controller  = $controller;
		return $this;
	}
	
	function applyInputMutator(array $data, string $current_op){	
		if ($current_op != 'CREATE' && $current_op != 'UPDATE'){
			throw new \InvalidArgumentException("Operation '$current_op' is invalid for Input Mutator");
		}

		foreach ($this->input_mutators as $field => list($fn, $apply_if_fn)){
			if (!in_array($field, $this->getAttr()))
				throw new \Exception("Invalid accesor: $field field is not present in " . $this->table_name); 

			$dato = $data[$field] ?? NULL;
					
			if ($apply_if_fn == null || $apply_if_fn(...[$current_op, $dato])){				
				$data[$field] = $fn($dato);
			} 				
		}

		return $data;
	}

	/*
		Es complicado hacerlo funcionar y falla cuando se selecciona un único registro
		quizás por el FETCH_MODE

		Está confirmado que si el FETCH_MODE no es ASSOC, va a fallar
	*/
	function applyOutputMutators($rows){
		if (empty($rows))
			return;
		
		if (empty($this->output_mutators))
			return $rows;

		//$by_id = in_array('id', $this->w_vars);	
		
		foreach ($this->output_mutators as $field => $fn){
			if (!in_array($field, $this->getAttr()))
				throw new \Exception("Invalid transformer: $field field is not present in " . $this->table_name); 

			if ($this->getFetchMode() == \PDO::FETCH_ASSOC){
				foreach ($rows as $k => $row){
					$rows[$k][$field] = $fn($row[$field]);
				}
			}elseif ($this->getFetchMode() == \PDO::FETCH_OBJ){
				foreach ($rows as $k => $row){
					$rows[$k]->$field = $fn($row->$field);
				}
			}			
		}
		return $rows;
	}
	
	function applyTransformer($rows){
		if (empty($rows))
			return;
		
		if (empty($this->transformer))
			return $rows;
		
		foreach ($rows as $k => $row){
			//var_dump($row);

			if (is_array($row))
				$row = (object) $row;

			$rows[$k] = $this->transformer->transform($row, $this->controller);
		}

		return $rows;
	}


	function setFetchMode(string $mode){
		$this->fetch_mode = constant("PDO::FETCH_{$mode}");
		return $this;
	}

	function assoc(){
		$this->fetch_mode = \PDO::FETCH_ASSOC;
		return $this;
	}

	protected function getFetchMode($mode_wished = null){
		if ($this->fetch_mode == NULL){
			if ($mode_wished != NULL) {
				return constant("PDO::FETCH_{$mode_wished}");
			} else {
				return $this->fetch_mode_default;
			}
		} else {
			return $this->fetch_mode;
		}
	}

	function setValidator(IValidator $validator){
		$this->validator = $validator;
		return $this;
	}

	function setTableAlias($tb_alias){
		$this->table_alias = $tb_alias;
		return $this;
	}

	// debe remover cualquier condición que involucre a $this->deletedAt en el WHERE !!!!
	function showDeleted($state = true){
		$this->show_deleted = $state;
		return $this;
	}

	function setSoftDelete(bool $status) {
		if (!$this->inSchema([$this->deletedAt])){
			if ($status){
				throw new SqlException("There is no $this->deletedAt for table '".$this->from()."' in the attr_types");
			}
		} 
		
		$this->soft_delete = $status;
		return $this;
	}
	

	/*
		Don't execute the query
	*/
	function dontExec(){
		$this->exec = false;
		return $this;
	}

	// set table and alias
	function table(string $table, $table_alias = null){
		$this->table_name = $table;
		$this->table_alias = $table_alias;
		return $this;		
	}

	protected function from(){
		if ($this->table_raw_q != null){
			return $this->table_raw_q;
		}

		if ($this->table_name == null){
			throw new \Exception("No table_name defined");
		}

		$tb_name = $this->table_name;

		if (DB::driver() == 'pgsql' && DB::schema() != null){
			$tb_name = DB::schema() . '.' . $tb_name;
		}

		$from = $this->table_alias != null ? $tb_name. ' as '.$this->table_alias : $tb_name.' ';  
		return $from;
	}

		
	/**
	 * unhide
	 * remove from hidden list of fields
	 * 
	 * @param  mixed $unhidden_fields
	 *
	 * @return void
	 */
	function unhide(array $unhidden_fields){
		if (!empty($this->hidden) && !empty($unhidden_fields)){			
			foreach ($unhidden_fields as $uf){
				$k = array_search($uf, $this->hidden);
				unset($this->hidden[$k]);
			}
		}
		return $this;
	}

	function unhideAll(){
		$this->hidden = [];
		return $this;
	}
	
	/**
	 * hide
	 * turn off field visibility from fetch methods 
	 * 
	 * @param  mixed $fields
	 *
	 * @return void
	 */
	function hide(array $fields){
		foreach ($fields as $f)
			$this->hidden[] = $f;

		return $this;	
	}

	
	/**
	 * fill
	 * makes a field fillable
	 *
	 * @param  mixed $fields
	 *
	 * @return object
	 */
	function fill(array $fields){
		foreach ($fields as $f)
			$this->fillable[] = $f;

		return $this;	
	}

	/*
		Make all fields fillable
	*/
	function fillAll(){
		$this->fillable = $this->attributes;
		return $this;	
	}
	
	/**
	 * unfill
	 * remove from fillable list of fields
	 * 
	 * @param  mixed $fields
	 *
	 * @return void
	 */
	function unfill(array $fields){
		if (!empty($this->fillable) && !empty($fields)){			
			foreach ($fields as $uf){
				$k = array_search($uf, $this->fillable);

				if ($k !== false){
					unset($this->fillable[$k]);
				}				
			}
		}

		return $this;
	}

	// INNER | LEFT | RIGTH JOIN
	function join($table, $on1 = null, $op = '=', $on2 = null, string $type = 'INNER JOIN') {
		// try auto-join
		if ($on1 == null && $on2 == null){
			if ($this->schema == NULL){
				throw new \Exception("Undefined schema for ". $this->table_name); 
			}

			if (!isset($this->schema['relationships'])){
				throw new \Exception("Undefined relationships for table '{$this->table_name}'"); 
			}

			$rel = $this->schema['relationships'];

			if (!isset($rel[$table])){				
				if (preg_match('/([a-zA-Z][a-zA-Z0-9]+) as ([a-zA-Z][a-zA-Z0-9]+)/', $table, $matches)){
					$tb = $matches[1];
					$fk = $matches[2];
				} else {
					throw new \Exception("Undefined relationship \$rel" . '["' . $table . '"]');
				}

				if (!isset($rel[$tb])){
					throw new \Exception("There is no explicit relationship between '{$this->table_name}' and '$tb'");
				}				
			}	
			
			if (!isset($fk)){
				if (count($rel[$table][0]) != 2){
					throw new \Exception("Unexpected number of arguments for relationship between {$this->table_name} and $table");
				}

				$on1 = $rel[$table][0][0];
				$on2 = $rel[$table][0][1];
			} else {
				$found = false;
				foreach ($rel[$tb] as $r){
					if (Strings::startsWith($fk, $r[0])){
						$found = true;
						//dd($r);
						[$on1, $on2] = $r;
						break;
					}
				}

				if (!$found){
					throw new \Exception("FK '$fk' in '$tb' not found!");
				}
			}			
		}

		$this->joins[] = [$table, $on1, $op, $on2, $type];
		return $this;
	}

	function leftJoin($table, $on1 = null, $op = '=', $on2 = null) {
		$this->join($table, $on1, $op, $on2, 'LEFT JOIN');
		return $this;
	}

	function rightJoin($table, $on1 = null, $op = '=', $on2 = null) {
		$this->join($table, $on1, $op, $on2, 'RIGHT JOIN');
		return $this;
	}

	function crossJoin($table) {
		$this->join($table, null, null, null, 'CROSS JOIN');
		return $this;
	}

	function naturalJoin($table) {
		$this->join($table, null, null, null, 'NATURAL JOIN');
		return $this;
	}
	
	function orderBy(array $o){
		$this->order = array_merge($this->order, $o);
		return $this;
	}

	function orderByRaw(string $o){
		$this->raw_order[] = $o;
		return $this;
	}


	function reorder(){
		$this->order = [];
		$this->raw_order = [];
		return $this;
	}

	function take(int $limit = null){
		if ($limit !== null){
			$this->limit = $limit;
		}

		return $this;
	}

	function limit(int $limit = null){
		return $this->take($limit);
	}

	function offset(int $n = null)
	{
		if ($n !== null){
			$this->offset = $n;
		}
		
		return $this;
	}

	function skip(int $n = null){
		return $this->offset($n);
	}

	function groupBy(array $g){
		$this->group = array_merge($this->group, $g);
		return $this;
	}

	function random(){
		$this->randomize = true;

		if (!empty($this->order))
			throw new SqlException("Random order is not compatible with OrderBy clausule");

		return $this;
	}

	function rand(){
		return $this->random();
	}

	function select(array $fields){
		$this->fields = $fields;
		return $this;
	}

	function addSelect(string $field){
		$this->fields[] = $field;
		return $this;
	}

	function selectRaw(string $q, array $vals = null){
		if (substr_count($q, '?') != count((array) $vals))
			throw new \InvalidArgumentException("Number of ? are not consitent with the number of passed values");
		
		$this->select_raw_q = $q;

		if ($vals != null)
			$this->select_raw_vals = $vals;

		return $this;
	}

	function whereRaw(string $q, array $vals = null){
		$qm = substr_count($q, '?'); 

		if ($qm !=0){
			if (!empty($vals)){
				if ($qm != count((array) $vals))
					throw new \InvalidArgumentException("Number of ? are not consitent with the number of passed values");
				
				$this->where_raw_vals = $vals;
			}else{
				if ($qm != count($this->to_merge_bindings))
					throw new \InvalidArgumentException("Number of ? are not consitent with the number of passed values");
					
				$this->where_raw_vals = $this->to_merge_bindings;		
			}

		}
		
		$this->where_raw_q = $q;
	
		return $this;
	}

	function whereExists(string $q, array $vals = null){
		$this->whereRaw("EXISTS $q", $vals);
		return $this;
	}

	function whereRegEx(string $field, $value){	
		$this->whereRaw("$field REGEXP ?", [$value]);
		return $this;
	}

	// alias
	function whereRegExp(string $field, $value){
		return $this->whereRegEx($field, $value);
	}

	function whereNotRegEx(string $field, $value){	
		$this->whereRaw("NOT $field REGEXP ?", [$value]);
		return $this;
	}

	// alias
	function whereNotRegExp(string $field, $value){
		return $this->whereNotRegEx($field, $value);
	}


	function havingRaw(string $q, array $vals = null){
		if (substr_count($q, '?') != count($vals))
			throw new \InvalidArgumentException("Number of ? are not consitent with the number of passed values");
		
		$this->having_raw_q = $q;

		if ($vals != null)
			$this->having_raw_vals = $vals;
			
		return $this;
	}

	function distinct(array $fields = null){
		if ($fields !=  null)
			$this->fields = $fields;
		
		$this->distinct = true;
		return $this;
	}

	function fromRaw(string $q){
		$this->table_raw_q = $q;
		return $this;
	}

	function union(Model $m){
		$this->union_type = 'NORMAL';
		$this->union_q = $m->toSql();
		$this->union_vals = $m->getBindings();
		return $this;
	}

	function unionAll(Model $m){
		$this->union_type = 'ALL';
		$this->union_q = $m->toSql();
		$this->union_vals = $m->getBindings();
		return $this;
	}

	function toSql(array $fields = null, array $order = null, int $limit = NULL, int $offset = null, bool $existance = false, $aggregate_func = null, $aggregate_field = null, $aggregate_field_alias = NULL)
	{		
		if (!empty($fields))
			$fields = array_merge($this->fields, $fields);
		else
			$fields = $this->fields;	

		$paginator = null;

		if (!$existance)
		{			
			// remove hidden			
			if (!empty($this->hidden)){			
			
				if (empty($this->select_raw_q)){
					if (empty($fields) && $aggregate_func == null) {
						$fields = $this->attributes;
					}
		
					foreach ($this->hidden as $h){
						$k = array_search($h, $fields);
						if ($k != null)
							unset($fields[$k]);
					}
				}			

			}
							
			if ($this->distinct){
				$remove = [$this->schema['id_name']];

				if ($this->inSchema([$this->createdAt]))
					$remove[] = $this->createdAt;

				if ($this->inSchema([$this->updatedAt]))
					$remove[] = $this->updatedAt;

				if ($this->inSchema([$this->deletedAt]))
					$remove[] = $this->deletedAt;

				if (!empty($fields)){
					$fields = array_diff($fields, $remove);
				}else{
					if (empty($aggregate_func))
						$fields = array_diff($this->getAttr(), $remove);
				}
			} 		


			if ($this->paginator){
				$order  = (!empty($order) && !$this->randomize) ? array_merge($this->order, $order) : $this->order;
				$limit  = $limit  ?? $this->limit  ?? null;
				$offset = $offset ?? $this->offset ?? 0; 
				
				if($limit>0 || $order!=NULL){
					try {
						$paginator = new Paginator();
						$paginator->setLimit($limit);
						$paginator->setOffset($offset);
						$paginator->setOrders($order);
						$paginator->setAttr($this->attributes);
						$paginator->compile();

						$this->pag_vals = $paginator->getBinding();
					}catch (SqlException $e){
						throw new SqlException("Pagination error: {$e->getMessage()}");
					}
				}else{
					$paginator = null;
				}
							
			} else {
				$paginator = null;	
			}	

		}			


		//dd($fields, 'FIELDS:');

		if (!$existance){
			if ($aggregate_func != null){
				if (strtoupper($aggregate_func) == 'COUNT'){					
					if ($aggregate_field == null)
						$aggregate_field = '*';

					//dd($fields, 'FIELDS:');
					//dd([$aggregate_field], 'AGGREGATE FIELD:');

					if (!empty($fields))
						$_f = implode(", ", $fields). ',';
					else
						$_f = '';

					if ($this->distinct)
						$q  = "SELECT $_f $aggregate_func(DISTINCT $aggregate_field)" . (!empty($aggregate_field_alias) ? " as $aggregate_field_alias" : '');
					else
						$q  = "SELECT $_f $aggregate_func($aggregate_field)" . (!empty($aggregate_field_alias) ? " as $aggregate_field_alias" : '');
				}else{
					if (!empty($fields))
						$_f = implode(", ", $fields). ',';
					else
						$_f = '';

					$q  = "SELECT $_f $aggregate_func($aggregate_field)" . (!empty($aggregate_field_alias) ? " as $aggregate_field_alias" : '');
				}
					
			}else{
				$q = 'SELECT ';

				//dd($fields);
				
				// SELECT RAW
				if (!empty($this->select_raw_q)){
					$distinct = ($this->distinct == true) ? 'DISTINCT' : '';
					$other_fields = !empty($fields) ? ', '.implode(", ", $fields) : '';
					$q  .= $distinct .' '.$this->select_raw_q. $other_fields;
				}else {
					if (empty($fields))
						$q  .= '*';
					else {
						$distinct = ($this->distinct == true) ? 'DISTINCT' : '';
						$q  .= $distinct.' '.implode(", ", $fields);
					}
				}					
			}
		} else {
			$q  = 'SELECT EXISTS (SELECT 1';
		}	

		$q  .= ' FROM '.$this->from();

		////////////////////////
		$values = array_merge($this->w_vals, $this->h_vals); 
		$vars   = array_merge($this->w_vars, $this->h_vars); 
		////////////////////////


		//dd($vars, 'VARS:');
		//dd($values, 'VALS:');

		// Validación
		if (!empty($this->validator)){
			$validado = $this->validator->validate($this->getRules(), array_combine($vars, $values));
			if ($validado !== true){
				throw new InvalidValidationException(json_encode($validado));
			} 
		}
		
		// JOINS
		$joins = '';
		foreach ($this->joins as $j){
			if ($j[4] == 'CROSS JOIN' || $j[4] == 'NATURAL JOIN'){
				$joins .= " $j[4] $j[0] ";
			} else {
				$joins .= " $j[4] $j[0] ON $j[1]$j[2]$j[3] ";
			}
		}

		$q  .= $joins;
		

		// WHERE
		$where_section = $this->whereFormedQuery();
		if (!empty($where_section)){

			// patch
			$where_section = str_replace(
							[
								'AND OR', 
								'(AND ',
								'(OR '
							], 
							[	'OR ',
								'( ',
								'( '
							], $where_section);

			$where_section = str_replace('(  NOT ', '(NOT ', $where_section);	

			$q  .= ' WHERE ' . $where_section;
		}
						
		$group = (!empty($this->group)) ? 'GROUP BY '.implode(',', $this->group) : '';
		$q  .= " $group";

	
		// HAVING
		$having_section = $this->havingFormedQuery();
		
		if (!empty($having_section)){

			// patch
			$having_section = str_replace(
							[
								'AND OR', 
								'(AND ',
								'(OR '
							], 
							[	'OR ',
								'( ',
								'( '
							], $having_section);

			$having_section = str_replace('(  NOT ', '(NOT ', $having_section);	

			$q  .= ' HAVING ' . $having_section;
		}

		if ($this->randomize){
			switch (DB::driver()){
				case 'mysql':
				case 'sqlite':
					$q .= ' ORDER BY RAND() ';
					break;
				case 'pgsql':
					$q .= ' ORDER BY RANDOM() ';
					break;
				default: 
					throw new \Exception("Invalid driver");	
			}
		} else {
			if (!empty($this->raw_order))
				$q .= ' ORDER BY '.implode(', ', $this->raw_order);
		}
		
		// UNION
		if (!empty($this->union_q)){
			$q .= 'UNION '.($this->union_type == 'ALL' ? 'ALL' : '').' '.$this->union_q.' ';
		}


		$q = rtrim($q);
		$q = Strings::removeRTrim('AND', $q);
		$q = Strings::removeRTrim('OR',  $q);


		// PAGINATION
		if (!$existance && $paginator!==null){
			$q .= $paginator->getQuery();
		}

		$q  = rtrim($q);
		
		if ($existance)
			$q .= ')';

		
		/*
		$q = rtrim($q);
		$q = String::removeRTrim('AND', $q);
		$q = String::removeRTrim('OR',  $q);
		*/

		//dd($q, 'Query:');
		//dd($vars, 'Vars:');
		//dd($values, 'Vals:');
		//var_dump($q);
		//exit;
		//var_export($vars);
		//var_export($values);
		
		$this->last_bindings = $this->getBindings();
		$this->last_pre_compiled_query = $q;
		return $q;	
	}

	function whereFormedQuery(){
		$where = '';
		
		if (!empty($this->where_raw_q))
			$where = $this->where_raw_q.' ';

		if (!empty($this->where)){
			$implode = '';

			$cnt = count($this->where);

			if ($cnt>0){
				$implode .= $this->where[0];
				for ($ix=1; $ix<$cnt; $ix++){
					$implode .= ' '.$this->where_group_op[$ix] . ' '.$this->where[$ix];
				}
			}			

			$where = trim($where);

			if (!empty($where)){
				$where = "($where) AND ". $implode. ' '; // <-------------
			}else{
				$where = "$implode ";
			}
		}			

		$where = trim($where);
		
		if ($this->inSchema([$this->deletedAt])){
			if (!$this->show_deleted){
				if (empty($where))
					$where = "{$this->deletedAt} IS NULL";
				else
					$where =  ($where[0]=='(' && $where[strlen($where)-1] ==')' ? $where :   "($where)" ) . " AND {$this->deletedAt} IS NULL";

			}
		}
		
		return ltrim($where);
	}

	function havingFormedQuery(){
		$having = '';
		
		if (!empty($this->having_raw_q))
			$having = $this->having_raw_q.' ';

		if (!empty($this->having)){
			$implode = '';

			$cnt = count($this->having);

			if ($cnt>0){
				$implode .= $this->having[0];
				for ($ix=1; $ix<$cnt; $ix++){
					$implode .= ' '.$this->having_group_op[$ix] . ' '.$this->having[$ix];
				}
			}			

			$having = trim($having);
			
			if (!empty($having)){
				$having = "($having) AND ". $implode. ' ';
			}else{
				$having = "$implode ";
			}
		}		

		return trim($having);
	}


	function getBindings()
	{	
		$pag = [];
		if (!empty($this->pag_vals)){
			switch (count($this->pag_vals)){
				case 2:
					$pag = [ $this->pag_vals[0][1], $this->pag_vals[1][1] ];
				break;
				case 1: 	
					$pag = [ $this->pag_vals[0][1] ];
				break;
			} 
		}
		
		$values = array_merge(	
								$this->select_raw_vals,
								$this->from_raw_vals,
								$this->where_raw_vals,
								$this->w_vals,
								$this->having_raw_vals,
								$this->h_vals,
								$pag
							);
		return $values;
	}

	//
	function mergeBindings(Model $model){
		$this->to_merge_bindings = $model->getBindings();

		if (!empty($this->table_raw_q)){
			$this->from_raw_vals = $this->to_merge_bindings;	
		}

		return $this;
	}

	/*
		 https://www.php.net/manual/en/pdo.constants.php

	*/
	protected function bind(string $q)
	{
		if ($this->conn == null){
			$this->connect();
		}

		$vals = array_merge($this->select_raw_vals, 
							$this->from_raw_vals, 
							$this->where_raw_vals,
							$this->w_vals,
							$this->having_raw_vals,
							$this->h_vals,
							$this->union_vals);

		///////////////[ BUG FIXES ]/////////////////

		$_vals = [];
		$reps  = 0;
		foreach($vals as $ix => $val)
		{				
			if($val === NULL){
				$q = Strings::replaceNth('?', 'NULL', $q, $ix+1-$reps);
				$reps++;

			/*
				Corrección para operaciones entre enteros y floats en PGSQL
			*/
			} elseif(DB::driver() == 'pgsql' && is_float($val)){ 
				$q = Strings::replaceNth('?', 'CAST(? AS DOUBLE PRECISION)', $q, $ix+1-$reps);
				$reps++;
				$_vals[] = $val;
			} else {
				$_vals[] = $val;
			}
		}

		$vals = $_vals;

		///////////////////////////////////////////

		
		$st = $this->conn->prepare($q);			
	
		foreach($vals as $ix => $val)
		{				
			if(is_null($val)){
				$type = \PDO::PARAM_NULL; // 0
			}elseif(is_int($val))
				$type = \PDO::PARAM_INT;  // 1
			elseif(is_bool($val))
				$type = \PDO::PARAM_BOOL; // 5
			elseif(is_string($val)){
				if(mb_strlen($val) < 4000){
					$type = \PDO::PARAM_STR;  // 2
				} else {
					$type = \PDO::PARAM_LOB;  // 3
				}
			}elseif(is_float($val))
				$type = \PDO::PARAM_STR;  // 2
			elseif(is_resource($val))	
				// https://stackoverflow.com/a/36724762/980631
				$type = \PDO::PARAM_LOB;  // 3
			elseif(is_array($val)){
				throw new \Exception("where value can not be an array!");				
			}else {
				var_dump($val);
				throw new \Exception("Unsupported type");
			}	

			$st->bindValue($ix +1 , $val, $type);
			//echo "Bind: ".($ix+1)." - $val ($type)\n";
		}

		$sh = count($vals);

		$bindings = $this->pag_vals;
		foreach($bindings as $ix => $binding){
			$st->bindValue($ix +1 +$sh, $binding[1], $binding[2]);
		}		
		
		return $st;	
	}

	function getLastPrecompiledQuery(){
		return $this->last_pre_compiled_query;
	}

	private function _dd($pre_compiled_sql, $bindings){		
		foreach($bindings as $ix => $val){			
			if(is_null($val)){
				$bindings[$ix] = 'NULL';
			}elseif(isset($vars[$ix]) && isset($this->schema['attr_types'][$vars[$ix]])){
				$const = $this->schema['attr_types'][$vars[$ix]];
				if ($const == 'STR')
					$bindings[$ix] = "'$val'";
			}elseif(is_int($val)){
				// pass
			}
			elseif(is_bool($val)){
				// pass
			} elseif(is_string($val))
				$bindings[$ix] = "'$val'";	
		}

		$sql = Arrays::str_replace_array('?', $bindings, $pre_compiled_sql);
		return trim(preg_replace('!\s+!', ' ', $sql)).';';
	}

	// Debug query
	function dd(){		
		return $this->_dd($this->toSql(), $this->getBindings());
	}

	// Debug last query
	function dd2(){		
		return $this->_dd($this->last_pre_compiled_query, $this->last_bindings);
	}

	// Debug last query
	function getLog(){
		return $this->dd2();
	}

	function getWhere(){
		return $this->where;
	}

	function get(array $fields = null, array $order = null, int $limit = NULL, int $offset = null, $pristine = false){
		$this->onReading();

		$q = $this->toSql($fields, $order, $limit, $offset);
		$st = $this->bind($q);

		$count = null;
		if ($this->exec && $st->execute()){
			$output = $st->fetchAll($this->getFetchMode());
			
			$count  = $st->rowCount();
			if (empty($output)) {
				$ret = [];
			}else {
				$ret = $pristine ? $output : $this->applyTransformer($this->applyOutputMutators($output));
			}

			$this->onRead($count);
		}else
			$ret = false;
				
		return $ret;	
	}

	function first(array $fields = null, $pristine = false){
		$this->onReading();

		$q = $this->toSql($fields, NULL);
		$st = $this->bind($q);

		$count = null;
		if ($this->exec && $st->execute()){
			$output = $st->fetch($this->getFetchMode());
			$count = $st->rowCount();

			if (empty($output)) {
				$ret = [];
			}else {
				$ret = $pristine ? $output : $this->applyTransformer($this->applyOutputMutators($output));
			}

			$this->onRead($count);
		}else
			$ret = false;
				
		return $ret;
	}

	function getOne(array $fields = null, $pristine = false){
		return $this->first($fields, $pristine);
	}
	
	function value($field){
		$this->onReading();

		$q = $this->toSql([$field], NULL, 1);
		$st = $this->bind($q);

		$count = null;
		if ($this->exec && $st->execute()) {
			$ret = $st->fetch(\PDO::FETCH_NUM)[0] ?? false;
			
			$count = $st->rowCount();
			$this->onRead($count);
		} else
			$ret = false;
			
		return $ret;	
	}

	function exists(){
		$q = $this->toSql(null, null, null, null, true);
		$st = $this->bind($q);

		if ($this->exec && $st->execute()){
			return (bool) $st->fetch(\PDO::FETCH_NUM)[0];
		}else
			return false;	
	}

	function pluck(string $field){
		$this->setFetchMode('COLUMN');
		$this->fields = [$field];

		$q = $this->toSql();
		$st = $this->bind($q);
	
		if ($this->exec && $st->execute()) {
			$res = $this->applyTransformer($this->applyOutputMutators($st->fetchAll($this->getFetchMode())));
			
			//var_dump($res);
			//exit;	
			
			return $res;
		} else
			return false;	
	}

	function avg($field, $alias = NULL){
		$q = $this->toSql(null, null, null, null, false, 'AVG', $field, $alias);
		$st = $this->bind($q);

		if (empty($this->group)){
			if ($this->exec && $st->execute())
				return $st->fetch($this->getFetchMode());
			else
				return false;	
		}else{
			if ($this->exec && $st->execute())
				return $st->fetchAll($this->getFetchMode());
			else
				return false;
		}	
	}

	function sum($field, $alias = NULL){
		$q = $this->toSql(null, null, null, null, false, 'SUM', $field, $alias);
		$st = $this->bind($q);

		if (empty($this->group)){
			if ($this->exec && $st->execute())
				return $st->fetch($this->getFetchMode());
			else
				return false;	
		}else{
			if ($this->exec && $st->execute())
				return $st->fetchAll($this->getFetchMode());
			else
				return false;
		}	
	}

	function min($field, $alias = NULL){
		$q = $this->toSql(null, null, null, null, false, 'MIN', $field, $alias);
		$st = $this->bind($q);

		if (empty($this->group)){
			if ($this->exec && $st->execute())
				return $st->fetch($this->getFetchMode());
			else
				return false;	
		}else{
			if ($this->exec && $st->execute())
				return $st->fetchAll($this->getFetchMode());
			else
				return false;
		}	
	}

	function max($field, $alias = NULL){
		$q = $this->toSql(null, null, null, null, false, 'MAX', $field, $alias);
		$st = $this->bind($q);

		if (empty($this->group)){
			if ($this->exec && $st->execute())
				return $st->fetch($this->getFetchMode());
			else
				return false;	
		}else{
			if ($this->exec && $st->execute())
				return $st->fetchAll($this->getFetchMode());
			else
				return false;
		}	
	}

	function count($field = NULL, $alias = NULL){
		$q = $this->toSql(null, null, null, null, false, 'COUNT', $field, $alias);
		$st = $this->bind($q);

		//dd($q, 'Q');
		//dd($this->table_raw_q, 'RAW Q');
		//exit;

		if (empty($this->group)){
			if ($this->exec && $st->execute()){
				return $st->fetch($this->getFetchMode('COLUMN'));
			}else
				return false;	
		}else{
			if ($this->exec && $st->execute()){
				return $st->fetchAll($this->getFetchMode('COLUMN'));
			}else
				return false;
		}	
	}

	function getWhereVals(){
		return $this->w_vals;
	}

	function getWhereVars(){
		return $this->w_vars;
	}

	function getWhereRawVals(){
		return $this->where_raw_vals;
	}

	function getHavingVals(){
		return $this->h_vals;
	}

	function getHavingVars(){
		return $this->h_vars;
	}

	function getHavingRawVals(){
		return $this->having_raw_vals;
	}

	// crea un grupo dentro del where
	function group(callable $closure, string $conjunction = 'AND', bool $negate = false) 
	{	
		$not = $negate ? ' NOT ' : '';

		$m = new Model();		
		call_user_func($closure, $m);	

		$w_formed 	= $m->whereFormedQuery();

		if (!empty($w_formed)){
			$w_vars   	= $m->getWhereVars();
			$w_vals   	= $m->getWhereVals();
			$w_raw_vals = $m->getWhereRawVals();

			$this->where[] = "$conjunction $not($w_formed)";	
			$this->w_vars  = array_merge($this->w_vars, $w_vars);
			$this->w_vals  = array_merge($this->w_vals, $w_raw_vals, $w_vals); // *
			
			$this->where_group_op[] = '';
		}


		$h_formed 	= $m->havingFormedQuery();

		if(!empty($h_formed)){
			$h_vars   	= $m->getHavingVars();
			$h_vals   	= $m->getHavingVals();
			$h_raw_vals = $m->getHavingRawVals();

			$this->having[] = "$conjunction $not($h_formed)";	
			$this->h_vars  = array_merge($this->h_vars, $h_vars);
			$this->h_vals  = array_merge($this->h_vals, $h_raw_vals, $h_vals); // *
			
			$this->having_group_op[] = '';
		}

		return $this;
	}

	function and(callable $closure){
		return $this->group($closure, 'AND', false);
	}

	function or(callable $closure){
		return $this->group($closure, 'OR', false);
	}

	function andNot(callable $closure){
		return $this->group($closure, 'AND', true);
	}

	// alias
	function not(callable $closure){
		return $this->andNot($closure);
	}

	function orNot(callable $closure){
		return $this->group($closure, 'OR', true);
	}


	function when($precondition = null, callable $closure = null, callable $closure2 = null){
		if (!empty($precondition)){			
			call_user_func($closure, $this);	
		} elseif ($closure2 != null){
			call_user_func($closure2, $this);
		}
		
		return $this;	
	}

	protected function _where($conditions = null, $group_op = 'AND', $conjunction = null)
	{
		if (empty($conditions)){
			return;
		}

		if (Arrays::is_assoc($conditions)){
			$conditions = Arrays::nonassoc($conditions);
		}

		if (isset($conditions[0]) && is_string($conditions[0]))
			$conditions = [$conditions];

		$_where = [];

		$vars   = [];
		$ops    = [];
		if (count($conditions)>0){
			if(is_array($conditions[Arrays::array_key_first($conditions)])){

				foreach ($conditions as $ix => $cond) {
					if ($cond[0] == null)
						throw new SqlException("Field can not be NULL");

					if(is_array($cond[1]) && (empty($cond[2]) || in_array($cond[2], ['IN', 'NOT IN']) ))
					{						
						if($this->schema['attr_types'][$cond[0]] == 'STR')	//
							$cond[1] = array_map(function($e){ return "'$e'";}, $cond[1]);   
						
						$in_val = implode(', ', $cond[1]);
						
						$op = isset($cond[2]) ? $cond[2] : 'IN';
						$_where[] = "$cond[0] $op ($in_val) ";	
					}else{
						$vars[]   = $cond[0];
						$this->w_vals[] = $cond[1];

						if ($cond[1] === NULL && (empty($cond[2]) || $cond[2]=='='))
							$ops[] = 'IS';
						else	
							$ops[] = $cond[2] ?? '=';
					}	
				}

			}else{
				$vars[]   = $conditions[0];
				$this->w_vals[] = $conditions[1];
		
				if ($conditions[1] === NULL && (empty($conditions[2]) || $conditions[2]== '='))
					$ops[] = 'IS';
				else	
					$ops[] = $conditions[2] ?? '='; 
			}	
		}

		foreach($vars as $ix => $var){
			$_where[] = "$var $ops[$ix] ?";
		}

		$this->w_vars = array_merge($this->w_vars, $vars); //

		////////////////////////////////////////////
		// group
		$ws_str = implode(" $conjunction ", $_where);
		
		if (count($conditions)>1 && !empty($ws_str))
			$ws_str = "($ws_str)";
		
		$this->where_group_op[] = $group_op;	

		$this->where[] = ' ' .$ws_str;
		////////////////////////////////////////////

		//dd($this->where, '$this->where');
		//dd($this->where_group_op, 'OPERATORS');
		//exit;
		//dd($this->w_vars, 'WHERE VARS');	
		//dd($this->w_vals, 'WHERE VALS');	

		return;
	}

	function whereColumn(string $field1, string $field2, string $op = '='){
		$validation = Factory::validador()->validate([ 
					'col1' => ['type' => 'alpha_num_dash'], 
					'col2' => ['type' => 'alpha_num_dash']
				],
				[
					'col1' => $field1, 
					'col2' => $field2
				]);

		if (!$validation){
			throw new InvalidValidationException(json_encode($validation));
		}

		if (!in_array($op, ['=', '>', '<', '<=', '>=', '!='])){
			throw new \InvalidArgumentException("Invalid operator '$op'");
		}	

		$this->where_raw_q = "{$field1}{$op}{$field2}";
		return $this;
	}

	function where($conditions, $conjunction = 'AND'){
		$this->_where($conditions, 'AND', $conjunction);
		return $this;
	}

	function orWhere($conditions, $conjunction = 'AND'){
		$this->_where($conditions, 'OR', $conjunction);
		return $this;
	}

	function whereOr($conditions){
		$this->_where($conditions, 'AND', 'OR');
		return $this;
	}

	// ok
	function orHaving($conditions, $conjunction = 'AND'){
		$this->_having($conditions, 'OR', $conjunction);
		return $this;
	}

	function orWhereRaw(string $q, array $vals = null){
		$this->or(function($x) use ($q, $vals){
			$x->whereRaw($q, $vals);
		});

		return $this;
	}

	function orHavingRaw(string $q, array $vals = null){
		$this->or(function($x) use ($q, $vals){
			$x->HavingRaw($q, $vals);
		});

		return $this;
	}

	function find($id){
		return $this->where([$this->schema['id_name'] => $id]);
	}

	function whereNull(string $field){
		$this->where([$field, NULL]);
		return $this;
	}

	function whereNotNull(string $field){
		$this->where([$field, NULL, 'IS NOT']);
		return $this;
	}

	function whereIn(string $field, array $vals){
		$this->where([$field, $vals, 'IN']);
		return $this;
	}

	function whereNotIn(string $field, array $vals){
		$this->where([$field, $vals, 'NOT IN']);
		return $this;
	}

	function whereBetween(string $field, array $vals){
		if (count($vals)!=2)
			throw new \InvalidArgumentException("whereBetween accepts an array of exactly two items");

		$min = min($vals[0],$vals[1]);
		$max = max($vals[0],$vals[1]);

		$this->where([$field, $min, '>=']);
		$this->where([$field, $max, '<=']);
		return $this;
	}

	function whereNotBetween(string $field, array $vals){
		if (count($vals)!=2)
			throw new \InvalidArgumentException("whereBetween accepts an array of exactly two items");

		$min = min($vals[0],$vals[1]);
		$max = max($vals[0],$vals[1]);

		$this->where([
						[$field, $min, '<'],
						[$field, $max, '>']
		], 'OR');
		return $this;
	}

	function oldest(){
		$this->orderBy([$this->createdAt => 'ASC']);
		return $this;
	}

	function latest(){
		$this->oldest();
		return $this;
	}

	function newest(){
		$this->orderBy([$this->createdAt => 'DESC']);
		return $this;
	}
	
	function _having(array $conditions = null, $group_op = 'AND', $conjunction = null)
	{	
		if (Arrays::is_assoc($conditions)){
            $conditions = Arrays::nonassoc($conditions);
        }

		if ((count($conditions) == 3 || count($conditions) == 2) && !is_array($conditions[1]))
			$conditions = [$conditions];
	
		//dd($conditions, 'COND:');

		$_having = [];
		foreach ((array) $conditions as $cond) {		
		
			if (Arrays::is_assoc($cond)){
				//dd($cond, 'COND PRE-CAMBIO');
				$cond[0] = Arrays::array_key_first($cond);
				$cond[1] = $cond[$cond[0]];

				//dd([$cond[0], $cond[1]], 'COND POST-CAMBIO');
			}
			
			$op = $cond[2] ?? '=';	
			
			$_having[] = "$cond[0] $op ?";
			$this->h_vars[] = $cond[0];
			$this->h_vals[] = $cond[1];
		}

		////////////////////////////////////////////
		// group
		$ws_str = implode(" $conjunction ", $_having);
		
		if (count($conditions)>1 && !empty($ws_str))
			$ws_str = "($ws_str)";
		
		$this->having_group_op[] = $group_op;	

		$this->having[] = ' ' .$ws_str;
		////////////////////////////////////////////

		//dd($this->having, 'HAVING:');
		//dd($this->h_vars, 'VARS');
		//dd($this->h_vals, 'VALUES');

		return $this;
	}

	function having(array $conditions, $conjunction = 'AND'){
		$this->_having($conditions, 'AND', $conjunction);
		return $this;
	}

	/*
		No admite eventos
	*/
	static function query(string $raw_sql){
		$conn = DB::getConnection();

		$query = $conn->query($raw_sql);

		$output = $query->fetchAll();
		return $output;
	}

	/**
	 * update
	 * It admits partial updates
	 *
	 * @param  array $data
	 *
	 * @return mixed
	 * 
	 */
	function update(array $data, $set_updated_at = true)
	{
		if ($this->conn == null)
			throw new SqlException('No conection');
			
		if (empty($data)){
			throw new SqlException('There is no data to update');
		}

		if (!Arrays::is_assoc($data)){
			throw new SqlException('Array of data should be associative');
		}
			
		if (isset($data[$this->createdBy]))
			unset($data[$this->createdBy]);
	

		$data = $this->applyInputMutator($data, 'UPDATE');
		$vars   = array_keys($data);
		$vals = array_values($data);

		
		//var_dump($data); ///
		//exit;

		if(!empty($this->fillable) && is_array($this->fillable)){
			foreach($vars as $var){
				if (!in_array($var,$this->fillable))
					throw new SqlException("update: $var is not fillable");
			}
		}

		// Validación
		if (!empty($this->validator)){
			$validado = $this->validator->validate($this->getRules(), $data);
			if ($validado !== true){
				throw new InvalidValidationException(json_encode($validado));
			} 
		}
	
		$this->data = $data;
		$this->onUpdating($data);
		
		$set = '';
		foreach($vars as $ix => $var){
			$set .= " $var = ?, ";
		}
		$set =trim(substr($set, 0, strlen($set)-2));

		if ($set_updated_at && $this->inSchema([$this->updatedAt])){
			$d = new \DateTime(NULL, new \DateTimeZone($this->config['DateTimeZone']));
			$at = $d->format('Y-m-d G:i:s');

			$set .= ", {$this->updatedAt} = '$at'";
		}

		$where = implode(' AND ', $this->where);

		$q = "UPDATE ".$this->from() .
				" SET $set WHERE " . $where;		


		$vals = array_merge($vals, $this->w_vals);
		$vars = array_merge($vars, $this->w_vars);		

		///////////////[ BUG FIXES ]/////////////////

		$_vals = [];
		$reps  = 0;
		foreach($vals as $ix => $val)
		{				
			if($val === NULL){
				$q = Strings::replaceNth('?', 'NULL', $q, $ix+1-$reps);
				$reps++;

			/*
				Corrección para operaciones entre enteros y floats en PGSQL
			*/
			} elseif(DB::driver() == 'pgsql' && is_float($val)){ 
				$q = Strings::replaceNth('?', 'CAST(? AS DOUBLE PRECISION)', $q, $ix+1-$reps);
				$reps++;
				$_vals[] = $val;
			} else {
				$_vals[] = $val;
			}
		}

		$vals = $_vals;

		///////////////////////////////////////////

	
		$st = $this->conn->prepare($q);


		//var_export($q);
		//var_export($vars);
		//var_export($vals);
		//exit;

		foreach($vals as $ix => $val){		
			if (is_array($val)){
				throw new \InvalidArgumentException("Invalid value. Can not be array: ". var_export($val, true));
			}
			
			if(is_null($val)){
				$type = \PDO::PARAM_NULL;
			}elseif(isset($vars[$ix]) && isset($this->schema['attr_types'][$vars[$ix]])){
				$const = $this->schema['attr_types'][$vars[$ix]];
				$type = constant("PDO::PARAM_{$const}");
			}elseif(is_int($val))
				$type = \PDO::PARAM_INT;
			elseif(is_bool($val))
				$type = \PDO::PARAM_BOOL;
			elseif(is_string($val))
				$type = \PDO::PARAM_STR;	

			$st->bindValue($ix+1, $val, $type);
			//echo "Bind: ".($ix+1)." - $val ($type)\n";
		}

		$this->last_bindings = $vals;
		$this->last_pre_compiled_query = $q;
	 
		if (!$this->exec){
			return 0;
		}

		if($st->execute()) {
			$count = $st->rowCount();
			$this->onUpdated($data, $count);
		} else 
			$count = false;
			
		return $count;
	}

	/**
	 * delete
	 *
	 * @param  array $data (aditional fields in case of soft-delete)
	 * @return mixed
	 */
	function delete(array $data = [])
	{
		if ($this->conn == null)
			throw new SqlException('No conection');

		// Validación
		if (!empty($this->validator)){
			$validado = $this->validator->validate($this->getRules(), array_combine($this->w_vars, $this->w_vals));
			if ($validado !== true){
				throw new InvalidValidationException(json_encode($validado));
			} 
		}

		$this->onDeleting();

		if ($this->soft_delete){
			$d = new \DateTime(NULL, new \DateTimeZone($this->config['DateTimeZone']));
			$at = $d->format('Y-m-d G:i:s');

			$to_fill = [];
			if (!empty($data)){
				$to_fill = array_keys($data);
			}
			$to_fill[] = $this->deletedAt;

			$data =  array_merge($data, [$this->deletedAt => $at]);

			$this->fill($to_fill);
			return $this->update($data, false);
		}

		$where = implode(' AND ', $this->where);

		$q = "DELETE FROM ". $this->from() . " WHERE " . $where;
		//dd($q);
		//exit;     ///////////////////

		
		$st = $this->bind($q);
	 
		$this->last_bindings = $this->getBindings();
		$this->last_pre_compiled_query = $q;

		if($this->exec && $st->execute()) {
			$count = $st->rowCount();
			$this->onDeleted($count);
		} else 
			$count = false;	
		
		return $count;	
	}

	/*
		@return mixed false | integer 
	*/
	function create(array $data, $ignore_duplicates = false)
	{
		if ($this->conn == null)
			throw new SqlException('No connection');

		if (!Arrays::is_assoc($data))
			throw new \InvalidArgumentException('Array of data should be associative');
	
		$this->data = $data;	

		//dd($data, 'DATA');
		//exit;
		
		$data = $this->applyInputMutator($data, 'CREATE');
		$vars = array_keys($data);
		$vals = array_values($data);

		if(!empty($this->fillable) && is_array($this->fillable)){
			foreach($vars as $var){
				if (!in_array($var,$this->fillable))
					throw new \InvalidArgumentException("$var is no fillable");
			}
		}

		// Validación
		if (!empty($this->validator)){
			$validado = $this->validator->validate($this->getRules(), $data);
			if ($validado !== true){
				throw new InvalidValidationException(json_encode($validado));
			} 
		}

		// Event hook
		$this->onCreating($data);

		$str_vars = implode(', ',$vars);

		$symbols = array_map(function($v){ return '?';}, $vars);
		$str_vals = implode(', ',$symbols);

		if ($this->inSchema([$this->createdAt])){
			$d = new \DateTime(NULL, new \DateTimeZone($this->config['DateTimeZone']));
			$at = $d->format('Y-m-d G:i:s');

			$str_vars .= ", {$this->createdAt}";
			$str_vals .= ", '$at'";
		}

		$q = "INSERT INTO " . $this->from() . " ($str_vars) VALUES ($str_vals)";
		$st = $this->conn->prepare($q);

		foreach($vals as $ix => $val){			
			if(is_null($val)){
				$type = \PDO::PARAM_NULL;
			}elseif(isset($vars[$ix]) && $this->schema != NULL && isset($this->schema['attr_types'][$vars[$ix]])){
				$const = $this->schema['attr_types'][$vars[$ix]];
				$type = constant("PDO::PARAM_{$const}");
			}elseif(is_int($val))
				$type = \PDO::PARAM_INT;
			elseif(is_bool($val))
				$type = \PDO::PARAM_BOOL;
			elseif(is_string($val))
				$type = \PDO::PARAM_STR;	

			//dd($type, "TYPE for $val");	

			$st->bindValue($ix+1, $val, $type);
		}

		$this->last_bindings = $vals;
		$this->last_pre_compiled_query = $q;


		if (!$this->exec){
			return NULL;
		}	

		if ($ignore_duplicates){
			try {
                $result = $st->execute();
            } catch (\PDOException $e){
                if (!Strings::contains('SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry', $e->getMessage())){
                    throw new \PDOException($e->getMessage());
                }
            }
		} else {
			$result = $st->execute();
		}
		
		if (!isset($result)){
			return;
		}

		if ($result){
			// sin schema no hay forma de saber la PRI Key. Intento con 'id' 
			$id_name = ($this->schema != NULL) ? $this->schema['id_name'] : 'id';		

			if (isset($data[$id_name])){
				$this->last_inserted_id =	$data[$id_name];
			} else {
				$this->last_inserted_id = $this->conn->lastInsertId();
			}

			$this->onCreated($data, $this->last_inserted_id);
		}else {
			$this->last_inserted_id = false;	
		}

		return $this->last_inserted_id;		
	}
	

	function insert(Array $data){
		return $this->create($data);
	}
	
	/*
		 to be called inside onUpdating() event hook

		 el problema es que necesito ejecutar el mismo WHERE que el UPDATE en un GET para seleccionar el mismo registro y tener contra que comparar.	

		 https://stackoverflow.com/questions/45702409/laravel-check-if-updateorcreate-performed-update/49350664#49350664
		 https://stackoverflow.com/questions/48793257/laravel-check-with-observer-if-column-was-changed-on-update/48793801
	*/	 

	function isDirty($fields = null) 
	{
		if ($fields == null){
			$fields = $this->attributes;
		}

		if (!is_array($fields)){
			$fields = [$fields];
		}

		// to be updated
		$keys = array_keys($this->data);

		if (!$this->inSchema($fields)){
			throw new \Exception("A field was not found in table {$this->table_name}");
		}
		
		$old_vals = $this->first($fields);
		foreach ($fields as $field){	
			if (!in_array($field, $keys)){
				continue;
			}

			$new_val = $this->data[$field];
			
			if ($new_val != $old_vals[$field]){
				return true;
			}	
		}

		return false;
	}


	/*
		Even hooks -podrían estar definidos en clase abstracta o interfaz-
	*/

	protected function boot() { }

	protected function onReading() { }
	protected function onRead(int $count) { }
	
	protected function onDeleting() { }
	protected function onDeleted(?int $count) { }

	protected function onCreating(Array &$data) {	}
	protected function onCreated(Array &$data, $last_inserted_id) { }

	protected function onUpdating(Array &$data) { }
	protected function onUpdated(Array &$data, ?int $count) { }


	function getSchema(){
		return $this->schema;
	}

	/*
		'''Reflection'''
	*/
	
	/**
	 * inSchema
	 *
	 * @param  array $props
	 *
	 * @return bool
	 */
	function inSchema(array $props){
		// debería chequear que la tabla exista

		if (empty($props))
			throw new \InvalidArgumentException("Attributes not found!");

		foreach ($props as $prop)
			if (!in_array($prop, $this->attributes)){
				return false; 
			}	
		
		return true;
	}

	/**
	 * getMissing
	 *
	 * @param  array $fields
	 *
	 * @return array
	 */
	function getMissing(array $fields){
		$diff =  array_diff($this->attributes, array_keys($fields));
		return array_diff($diff, $this->schema['nullable']);
	}
	
	/**
	 * Get attr_types 
	 */ 
	function getAttr()
	{
		return $this->attributes;
	}

	function getIdName(){
		return $this->schema['id_name'];
	}

	function getNotHidden(){
		return array_diff($this->attributes, $this->hidden);
	}

	function isNullable(string $field){
		return in_array($field, $this->schema['nullable']);
	}

	function isFillable(string $field){
		return in_array($field, $this->fillable);
	}

	function getFillables(){
		return $this->fillable;
	}

	function setNullables(Array $arr){
		$this->schema['nullable'] = $arr;
	}

	function addNullables(Array $arr){
		$this->schema['nullable'] = array_merge($this->schema['nullable'], $arr);
	}

	function removeNullables(Array $arr){
		$this->schema['nullable'] = array_diff($this->schema['nullable'], $arr);
	}

	function getNullables(){
		return $this->schema['nullable'];
	}

	function getNotNullables(){
		return array_diff($this->attributes, $this->schema['nullable']);
	}

	function getRules(){
		return $this->schema['rules'] ?? NULL;
	}

	function getRule(string $name){
		return $this->schema['rules'][$name] ?? NULL;
	}

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

}
<?php

namespace Boctulus\Simplerest\Core\Traits;

use Boctulus\Simplerest\Core\Exceptions\ColumnTableNotFoundException;
use Boctulus\Simplerest\Core\Exceptions\InvalidValidationException;
use Boctulus\Simplerest\Core\Exceptions\SchemaException;
use Boctulus\Simplerest\Core\Exceptions\SqlException;
use Boctulus\Simplerest\Core\Interfaces\ITransformer;
use Boctulus\Simplerest\Core\Interfaces\IValidator;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Paginator;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Core\Libs\Validator;
use Boctulus\Simplerest\Core\Model;

trait QueryBuilderTrait
{
	use ExceptionHandler;

	/**
	 * Estados de ejecución para las operaciones de escritura
	 */

	const EXECUTION_MODE_NORMAL   = 0;    // Ejecución normal
	const EXECUTION_MODE_SIMULATE = 1;   // Simular operación (no realiza cambios en BD)
	const EXECUTION_MODE_PREVIEW  = 2;   // Obtener SQL y valores que se ejecutarían

	const DECIMAL_AS_STRING = false; // false = sin comillas (default), true = con comillas

	public    $exec = true;
	public    $subquery_aliases = [];

	// for internal use
	protected $table_name;
	protected $table_alias = [];
	protected $prefix;

	protected $executionMode = self::EXECUTION_MODE_NORMAL;

	protected $fillable     = [];
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
	protected $join_raw = [];
	protected $aggregate_field_alias;
	protected $randomize = false;
	protected $distinct  = false;
	protected $to_merge_bindings = [];
	protected $last_pre_compiled_query;
	protected $last_bindings = [];
	protected $last_compiled_sql;
	protected $limit;
	protected $offset;
	protected $pag_vals = [];
	protected $validator;
	protected $input_mutators = [];
	protected $output_mutators = [];
	protected $transformer;
	protected $controller;
	protected $bind = true;
	protected $strict_mode_having = false;
	protected $enable_qualification = false; //
	protected $semicolon_ending = false;
	protected $fetch_mode;
	protected $soft_delete;
	protected $last_inserted_id;
	protected $paginator = true;
	protected $fetch_mode_default = \PDO::FETCH_ASSOC;
	protected $last_operation;
	protected $current_operation;
	protected $insert_vars = [];
	protected $data = [];

	protected $having_group_op;
	protected $wrap_fields = false;
	protected $config;

	protected $createdAt = 'created_at';
	protected $updatedAt = 'updated_at';
	protected $deletedAt = 'deleted_at';
	protected $createdBy = 'created_by';
	protected $updatedBy = 'updated_by';
	protected $deletedBy = 'deleted_by';
	protected $is_locked = 'is_locked';
	protected $belongsTo = 'belongs_to';

	/*	
		Nombres de los campos

		Ej:

		[
			"created_at" => "Created At",
			// ...
		]
	*/
	protected        $field_names = [];

	/*
		Solo como indicaicon para el FrontEnd
	*/
	protected 		 $field_order = [];

	/*
		Aca se especificaria si es un checkbox o radiobox por ejemplo

		Para tipo "dropdown" o "list" se utilizarian los valores de la regla de validacion
		o de la tabla relacionada 

		Tambien otros formatters que puedan estar disponibles en el frontend
	*/
	protected        $formatters = [];

	static protected $sql_formatter_callback;
	protected        $sql_formatter_status;
	protected 		 $decimal_as_string = null;

	protected        $connect_to = [];

	static protected $current_sql;

	/*	
		Returns table or its alias if exists for the referenced table
	*/
	function getTableAlias()
	{
		if (isset($this->table_alias[$this->table_name])) {
			$tb_name = $this->table_alias[$this->table_name];
		} else {
			$tb_name = $this->table_name;
		}

		return $tb_name;
	}

	protected function getFullyQualifiedField(string $field)
	{
		if (!$this->enable_qualification) {
			return $field;
		}

		if (!Strings::contains('.', $field)) {
			$tb_name = $this->getTableAlias();

			return "{$tb_name}.$field";
		} else {
			return $field;
		}
	}

	protected function unqualifyField(string $field)
	{
		if (Strings::contains('.', $field)) {
			$_f = explode('.', $field);
			return $_f[1];
		}

		return $field;
	}

	function addRules(ValidationRules $vr)
	{
		$this->schema['rules'] = array_merge($this->schema['rules'], $vr->getRules());
	}

	function noValidation()
	{
		$this->validator = null;
		return $this;
	}

	/*
		Returns prmary key
	*/
	function getKeyName()
	{
		return $this->schema['id_name'];
	}

	function getTableName()
	{
		return $this->table_name;
	}

	/*
		Turns on / off pagination
	*/
	function setPaginator(bool $status)
	{
		$this->paginator = $status;
		return $this;
	}

	function registerInputMutator(string $field, callable $fn, ?callable $apply_if_fn)
	{
		$this->input_mutators[$field] = [$fn, $apply_if_fn];
		return $this;
	}

	function registerOutputMutator(string $field, callable $fn)
	{
		$this->output_mutators[$field] = $fn;
		return $this;
	}

	// acepta un Transformer
	function registerTransformer(ITransformer $t, $controller = NULL)
	{
		$this->unhideAll();
		$this->transformer = $t;
		$this->controller  = $controller;
		return $this;
	}

	function applyInputMutator(array $data, string $current_op)
	{
		if ($current_op != 'CREATE' && $current_op != 'UPDATE') {
			throw new \InvalidArgumentException("Invalid operation '$current_op'");
		}

		foreach ($this->input_mutators as $field => list($fn, $apply_if_fn)) {
			if (!in_array($field, $this->getAttr()))
				throw new ColumnTableNotFoundException("Accesor error. Column '$field' is not present in " . $this->table_name);

			$dato = $data[$field] ?? NULL;

			if ($apply_if_fn == null || $apply_if_fn(...[$current_op, $dato])) {
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
	function applyOutputMutators($rows)
	{
		if (empty($rows))
			return;

		if (empty($this->output_mutators))
			return $rows;

		//$by_id = in_array('id', $this->w_vars);	

		foreach ($this->output_mutators as $field => $fn) {
			if (!in_array($field, $this->getAttr()))
				throw new ColumnTableNotFoundException("Transformer error. Field '$field' is not present in " . $this->table_name);

			if ($this->getFetchMode() == \PDO::FETCH_ASSOC) {
				foreach ($rows as $k => $row) {
					$rows[$k][$field] = $fn($row[$field]);
				}
			} elseif ($this->getFetchMode() == \PDO::FETCH_OBJ) {
				foreach ($rows as $k => $row) {
					$rows[$k]->$field = $fn($row->$field);
				}
			}
		}
		return $rows;
	}

	function applyTransformer($rows)
	{
		if (empty($rows))
			return;

		if (empty($this->transformer))
			return $rows;

		foreach ($rows as $k => $row) {
			//var_dump($row);

			if (is_array($row))
				$row = (object) $row;

			$rows[$k] = $this->transformer->transform($row, $this->controller);
		}

		return $rows;
	}


	function setFetchMode(string $mode)
	{
		$this->fetch_mode = constant("PDO::FETCH_{$mode}");
		return $this;
	}

	function assoc()
	{
		$this->fetch_mode = \PDO::FETCH_ASSOC;
		return $this;
	}

	/*
		Ej:
		
		$contact = DB::table('contacts')                    
        ->where('id', $id)
        ->asObject()   // devuelve objeto
        ->first();
        
        $newStatus = !$contact->favorite;
	*/
	function asObject()
	{
		$this->setFetchMode('OBJ');
		return $this;
	}

	function column()
	{
		$this->fetch_mode = \PDO::FETCH_COLUMN;
		return $this;
	}

	protected function getFetchMode($mode_wished = null)
	{
		if ($this->fetch_mode == NULL) {
			if ($mode_wished != NULL) {
				return constant("PDO::FETCH_{$mode_wished}");
			} else {
				return $this->fetch_mode_default;
			}
		} else {
			return $this->fetch_mode;
		}
	}

	function setValidator(?IValidator $validator = null)
	{
		$this->validator = $validator;
		return $this;
	}

	function setTableAlias(string $tb_alias, ?string $table = null)
	{
		if ($table === null) {
			$table = $this->table_name;
		}

		$this->table_alias[$table] = $tb_alias;
		return $this;
	}

	function alias(string $tb_alias, ?string $table = null)
	{
		return $this->setTableAlias($tb_alias, $table);
	}

	/*
		Don't execute the query
	*/
	function dontExec()
	{
		$this->exec = false;
		return $this;
	}

	/*
		Don't bind params
	*/
	function dontBind()
	{
		$this->bind = false;
		return $this;
	}

	function doBind()
	{
		$this->bind = true;
		return $this;
	}

	function setStrictModeHaving(bool $state)
	{
		$this->strict_mode_having = $state;
		return $this;
	}

	function dontQualify()
	{
		$this->enable_qualification = false;
		return $this;
	}

	function qualify()
	{
		$this->enable_qualification = true;
		return $this;
	}

	// set table and alias
	function table(string $table, $table_alias = null)
	{
		$this->table_name          = $table;
		$this->table_alias[$table] = $table_alias;

		if (!empty($this->prefix)) {
			$this->table_name = $this->prefix . $this->table_name;
			$this->prefix     = null;
		}

		return $this;
	}

	// alias for table();
	function setTable(string $table, $table_alias = null)
	{
		return $this->table($table, $table_alias);
	}

	function prefix($prefix = '')
	{
		$this->prefix     = $prefix;
		return $this;
	}

	// alias for prefix()
	function setPrefix($prefix = '')
	{
		return $this->prefix($prefix);
	}

	function removePrefix(string $prefix)
	{
		$table = Strings::after($this->table_name, $prefix);
		$this->table($table);
		$this->prefix = null;

		return $this;
	}

	protected function from()
	{
		if ($this->table_raw_q != null) {
			return $this->table_raw_q;
		}

		if ($this->table_name == null) {
			throw new \Exception("No table_name defined");
		}

		$tb_name = $this->table_name;

		if (DB::driver() == DB::PGSQL && DB::schema() != null) {
			$tb_name = DB::schema() . '.' . $tb_name;
		}

		$from = isset($this->table_alias[$this->table_name]) ? ($tb_name . ' as ' . $this->table_alias[$this->table_name]) : $tb_name . ' ';
		return trim($from);
	}

	function fromRaw(string $q)
	{
		$this->table_raw_q = $q;
		return $this;
	}

	/**
	 * unhide
	 * remove from hidden list of fields
	 * 
	 * @param  mixed $unhidden_fields
	 *
	 * @return void
	 */
	function unhide(array $unhidden_fields): Model
	{
		if (!empty($this->hidden) && !empty($unhidden_fields)) {
			foreach ($unhidden_fields as $uf) {
				$k = array_search($uf, $this->hidden);
				unset($this->hidden[$k]);
			}
		}
		return $this;
	}

	function unhideAll(): Model
	{
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
	function hide(array $fields): Model
	{
		foreach ($fields as $f) {
			if (!in_array($f, $this->hidden)) {
				$this->hidden[] = $f;
			}
		}

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
	function fill(array $fields)
	{
		foreach ($fields as $f) {
			if (!in_array($f, $this->fillable)) {
				$this->fillable[] = $f;
			}

			/*
				Remuevo los campos fillables del array de los no-fillables	
			*/
			$pos = array_search($f, $this->not_fillable);

			if ($pos !== false) {
				unset($this->not_fillable[$pos]);
			}
		}

		return $this;
	}

	function unfill(array $fields)
	{
		foreach ($fields as $f) {
			if (!in_array($f, $this->not_fillable)) {
				$this->not_fillable[] = $f;
			}

			/*
				Remuevo los campos no-fillables del array de los fillables	
			*/
			$pos = array_search($f, $this->fillable);

			if ($pos !== false) {
				unset($this->fillable[$pos]);
			}
		}

		return $this;
	}

	/*
		Make all fields fillable
	*/
	function fillAll()
	{
		$this->fillable     = $this->attributes;
		$this->not_fillable = [];

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
	protected function unfillAll(array $fields)
	{
		if (!empty($this->fillable) && !empty($fields)) {
			foreach ($this->fillable as $ix => $f) {
				foreach ($fields as $to_unset) {
					if ($f == $to_unset) {
						if (!in_array($f, $this->not_fillable)) {
							$this->not_fillable[] = $f;
						}

						unset($this->fillable[$ix]);
						break;
					}
				}
			}
		}

		return $this;
	}


	// INNER | LEFT | RIGTH JOIN
	function join($table, $on1 = null, $op = '=', $on2 = null, string $type = 'INNER JOIN')
	{
		$_table     = null;
		$this_alias = null;

		if (preg_match('/([a-z0-9_]+) as ([a-z0-9_]+)/i', $table, $matches)) {
			$_table     = $matches[0];
			$table      = $matches[1];
			$this_alias = $matches[2];
		}

		$on_replace = function (&$on) use ($this_alias, $table) {
			if (empty($on)) {
				throw new \InvalidArgumentException("Paramter 1 in on_replace can not be null or empty");
			}

			$_on = explode('.', $on);

			if (count($_on) != 2) {
				throw new \InvalidArgumentException("Paramter 1 format in on_replace is not well-formatted.");
			}

			if (isset($this->table_alias[$this->table_name])) {
				if ($_on[0] ==  $this->table_name) {
					$on = $this->table_alias[$this->table_name] . '.' . $_on[1];
				}
			}

			if (!is_null($this_alias)) {
				if ($_on[0] ==  $table) {
					$on = $this_alias . '.' . $_on[1];
				}
			}
		};

		// try auto-join
		if ($on1 == null && $on2 == null) {
			if ($this->schema == NULL) {
				throw new \Exception("Undefined schema for " . $this->table_name);
			}

			if (!isset($this->schema['relationships'])) {
				throw new \Exception("Undefined relationships for table '{$this->table_name}'");
			}

			$rel   = $this->schema['relationships'];
			$pivot = get_pivot([$this->table_name, $table], DB::getCurrentConnectionId());

			// Si la relación no existe => podría ser N:M o no existir
			if (!isset($rel[$table])) {
				// **
				// Podría ser una relación N:M si hay pivote o...  1:1, 1:N

				if (!is_null($pivot)) {
					// Relación N:M

					$bridge = $pivot['bridge'];
					$rels   = $pivot['relationships'];

					$keys = array_keys($rels);

					if ($keys[0] == $table) {
						$rels = array_reverse($rels);
					}

					foreach ($rels as $tb => $rel) {
						if ($tb == $table) {
							if (!is_null($_table)) {
								$t = $_table;
							} else {
								$t = $table;
							}
						} else {
							$t = $bridge;
						}

						$on1 = $rel[0][0];
						$on2 = $rel[0][1];

						$on_replace($on1);
						$on_replace($on2);

						$this->join($t, $on1, '=', $on2, $type);
					}

					return $this;
				} else {
					// NUNCA DEBERÍA LLEGAR ACÁ PORQUE O ES N:M o NADA
				}
			} // else...


			$relx  = $this->schema['expanded_relationships'];

			if (!isset($relx[$table])) {
				throw new \Exception("Table '$table' is not 'present' in {$this->table_name}'s schema as if it had a relationship with it");
			}

			$relxs = $relx[$table];

			//dd($rels, 'RELS'); ///

			if (count($relxs) >= 2) {
				//dd("Relación multiple entre las mismas dos tablas"); //

				foreach ($relxs as $r) {
					if (!isset($r[0]['alias'])) {
						$alias = '__' . $r[0][1];
					} else {
						$alias = $r[0]['alias'];
					}

					$on1 = "{$alias}.{$r[0][1]}";
					$on2 = "{$r[1][0]}.{$r[1][1]}";

					$ori_tb_name = $table;
					$_table = "$ori_tb_name as $alias";

					// dd([
					// 	'table' => $_table,
					// 	'on1' => $on1,
					// 	'on2' => $on2,
					// 	'alias' => $alias
					// ]);

					$this->joins[] = [$_table, $on1, $op, $on2, $type];
				}


				return $this;
			} else {
				$on1 = $rel[$table][0][0];
				$on2 = $rel[$table][0][1];
			}
		}

		if (!is_null($_table)) {
			$table = $_table;
		}

		$on_replace($on1);
		$on_replace($on2);

		$this->joins[] = [$table, $on1, $op, $on2, $type];
		return $this;
	}

	function joinRaw(string $str)
	{
		$this->join_raw[] = $str;
		return $this;
	}

	function leftJoin($table, $on1 = null, $op = '=', $on2 = null)
	{
		$this->join($table, $on1, $op, $on2, 'LEFT JOIN');
		return $this;
	}

	function rightJoin($table, $on1 = null, $op = '=', $on2 = null)
	{
		$this->join($table, $on1, $op, $on2, 'RIGHT JOIN');
		return $this;
	}

	/*
		FULL (OUTER) JOIN puede ser emulado en MySQL

		https://stackoverflow.com/questions/7978663/mysql-full-join/36001694
	*/

	function crossJoin($table)
	{
		$this->joins[] = [$table, null, null, null, 'CROSS JOIN'];
		return $this;
	}

	function naturalJoin($table)
	{
		$this->joins[] = [$table, null, null, null, 'NATURAL JOIN'];;
		return $this;
	}

	function joinTo(...$tables)
	{
		if (is_array($tables[0])) {
			$tables = $tables[0];
		}

		foreach ($tables as $tb) {
			$this->join($tb);
		}

		return $this;
	}

	function orderBy($o)
	{
		if (is_string($o)) {
			$o = array_map('trim', explode(',', $o));
		}

		$this->order = array_merge($this->order, $o);
		return $this;
	}

	function orderByRaw(string $o)
	{
		$this->raw_order[] = $o;
		return $this;
	}

	function orderByAsc(string $field)
	{	
		$this->order = array_merge($this->order, [$field, 'ASC']);
		return $this;
	}

	function orderByDesc(string $field)
	{	
		$this->order = array_merge($this->order, [$field, 'DESC']);
		return $this;
	}

	function reorder()
	{
		$this->order = [];
		$this->raw_order = [];
		return $this;
	}

	function take($limit = null)
	{
		if ($limit !== null) {
			$this->limit = $limit;
		}

		return $this;
	}

	function limit($limit = null)
	{
		return $this->take($limit);
	}

	function offset($n = null)
	{
		if ($n !== null) {
			$this->offset = $n;
		}

		return $this;
	}

	function skip($n = null)
	{
		return $this->offset($n);
	}

	function paginate(int $page, ?int $page_size = null)
	{
		if ($page_size === null) {
			$page_size = Config::get()['paginator']['default_limit'] ?? 10;
		}

		$this->limit  = $page_size;
		$this->offset = Paginator::calcOffset($page, $page_size);

		return $this;
	}

	function groupBy(array $g)
	{
		$this->group = array_merge($this->group, $g);
		return $this;
	}

	function random()
	{
		$this->randomize = true;

		if (!empty($this->order))
			throw new SqlException("Random order is not compatible with OrderBy clausule");

		return $this;
	}

	function rand()
	{
		return $this->random();
	}

	/*
		Formas validas de enviar fields:

		$model->select('id, name, email'); // Laravel style
		$model->select(['id', 'name', 'email']); // as array
		$model->select('id', 'name', 'email'); // as string
	*/
	function select($fields)
	{
		// Captura todos los argumentos pasados
		$args = func_get_args();

		if (count($args) > 1) {
			// Si hay más de un argumento, los tratamos como lista de campos
			$fields = array_map('trim', $args);
		} else {
			// Solo un argumento
			$fields = $args[0];

			if (is_string($fields)) {
				// Si es una cadena, dividir por coma
				$fields = array_map('trim', explode(',', $fields));
			} elseif (!is_array($fields)) {
				// Cualquier otro tipo (seguridad)
				$fields = [$fields];
			}
		}

		$this->fields = $fields;
		return $this;
	}

	function addSelect(string $field)
	{
		$this->fields[] = $field;
		return $this;
	}

	function selectRaw(string $q, $vals = null)
	{
		if (substr_count($q, '?') != count((array) $vals))
			throw new \InvalidArgumentException("Number of ? are not consitent with the number of passed values");

		if (empty($this->select_raw_q)) {
			$this->select_raw_q = $q;

			if ($vals != null) {
				$this->select_raw_vals = $vals;
			}
		} else {
			$this->select_raw_q = "{$this->select_raw_q}, $q";

			if ($vals != null) {
				$this->select_raw_vals = array_merge($this->select_raw_vals, $vals);
			}
		}

		return $this;
	}

	function whereRaw(string $q, $vals = null)
	{
		$qm = substr_count($q, '?');

		if ($qm != 0) {
			if (!empty($vals)) {
				if ($qm != count((array) $vals))
					throw new \InvalidArgumentException("Number of ? are not consitent with the number of passed values");

				$this->where_raw_vals = $vals;
			} else {
				if ($qm != count($this->to_merge_bindings))
					throw new \InvalidArgumentException("Number of ? are not consitent with the number of passed values");

				$this->where_raw_vals = $this->to_merge_bindings;
			}
		}

		$this->where_raw_q = $q;

		return $this;
	}

	/*
		Revisar contra:

		https://laravel.com/docs/9.x/queries#where-exists-clauses
	*/
	function whereExists(string $q, array $vals = null)
	{
		$this->whereRaw("EXISTS $q", $vals);
		return $this;
	}

	function whereRegEx(string $field, $value)
	{
		$this->whereRaw("$field REGEXP ?", [$value]);
		return $this;
	}

	// alias
	function whereRegExp(string $field, $value)
	{
		return $this->whereRegEx($field, $value);
	}

	function whereNotRegEx(string $field, $value)
	{
		$this->whereRaw("NOT $field REGEXP ?", [$value]);
		return $this;
	}

	// alias
	function whereNotRegExp(string $field, $value)
	{
		return $this->whereNotRegEx($field, $value);
	}

	/*	
		Implementar también:

		whereDay()
		whereMonth()
		whereYear()
		whereTime()
	*/
	function whereDate(string $field, string $value, string $operator = '=')
	{
		if (!in_array($operator, ['=', '>', '<'])) {
			throw new \InvalidArgumentException("Invalid operator: '$operator' is invalid for date comparissions");
		}

		$len = strlen($value);

		if ($this->schema !== null) {
			if (!isset($this->schema['rules'][$field])) {
				throw new \InvalidArgumentException("Unknown field '$field'");
			}
		}

		if ($len === 10) {
			if (!(new Validator())->isType($value, 'date')) {
				throw new \InvalidArgumentException("Invalid type: '$value' is not a date");
			}

			switch ($this->schema['rules'][$field]['type']) {
				case 'date':
					return $this->where([$field, $value, $operator]);
				case 'datetime':
					switch ($operator) {
						case '=':
							return $this->where([$field, $value . '%', 'LIKE']);
						case '>':
							$value = (new \DateTime("$value +1 day"))->format('Y-m-d H:i:s');
							return $this->where([$field, $value, '>']);
						case '<':
							$value = (new \DateTime("$value -1 day"))->format('Y-m-d H:i:s');
							return $this->where([$field, $value, '<']);
					}
				default:
					throw new \InvalidArgumentException("Filed type '$field' is not a date or datetime");
			}
		} else if ($len === 19) {
			if (!(new Validator())->isType($value, 'datetime')) {
				throw new \InvalidArgumentException("Invalid type: '$value' is not a date");
			}

			switch ($this->schema['rules'][$field]['type']) {
				case 'date':
					throw new \InvalidArgumentException("Presition can not exced yyyy-mm-dd");
				case 'datetime':
					switch ($operator) {
						case '=':
							return $this->where([$field, $value . '%', 'LIKE']);
						case '>':
							return $this->where([$field, $value, '>']);
						case '<':
							return $this->where([$field, $value, '<']);
					}
				default:
					throw new \InvalidArgumentException("Filed type '$field' is not a date or datetime");
			}
		} else {
			throw new \InvalidArgumentException("Invalid type: '$value' is not a date or datetime");
		}
	}

	function distinct(array $fields = null)
	{
		if ($fields !=  null)
			$this->fields = $fields;

		$this->distinct = true;
		return $this;
	}

	function union(Model $m)
	{
		$this->union_type = 'NORMAL';
		$this->union_q = $m->toSql();
		$this->union_vals = $m->getBindings();
		return $this;
	}

	function unionAll(Model $m)
	{
		$this->union_type = 'ALL';
		$this->union_q = $m->toSql();
		$this->union_vals = $m->getBindings();
		return $this;
	}

	function toSql(array $fields = null, array $order = null, int $limit = NULL, int $offset = null, bool $existance = false, $aggregate_func = null, $aggregate_field = null, $aggregate_field_alias = NULL)
	{
		$this->aggregate_field_alias = $aggregate_field_alias;

		// dd($this->table_name, "TABLE NAME ======================>");

		if (!empty($fields))
			$fields = array_merge($this->fields, $fields);
		else
			$fields = $this->fields;

		if (!empty($fields)) {
			$fields = array_map(function ($field) {
				return $this->getWrapFieldName($field);
			}, $fields);
		}

		$paginator = null;

		if (!$existance) {
			// remove hidden			
			if (!empty($this->hidden)) {

				if (empty($this->select_raw_q)) {
					if (empty($fields) && $aggregate_func == null) {
						$fields = $this->attributes;
					}

					foreach ($this->hidden as $h) {
						$k = array_search($h, $fields);
						if ($k != null)
							unset($fields[$k]);
					}
				}
			}

			if ($this->distinct) {
				$remove = [];

				// Verificar que el schema no sea null antes de acceder
				if ($this->schema !== null && isset($this->schema['id_name'])) {
					$remove[] = $this->schema['id_name'];
				}

				if ($this->inSchema([$this->createdAt]))
					$remove[] = $this->createdAt;

				if ($this->inSchema([$this->updatedAt]))
					$remove[] = $this->updatedAt;

				if ($this->inSchema([$this->deletedAt]))
					$remove[] = $this->deletedAt;

				if (!empty($fields)) {
					if (!empty($aggregate_func)) {
						$fields = array_diff($this->getAttr(), $remove);
					} else {
						$fields = array_diff($fields, $remove);
					}
				}
			}

			if ($this->paginator) {
				$order  = (!empty($order) && !$this->randomize) ? array_merge($this->order, $order) : $this->order;
				$limit  = $limit  ?? $this->limit  ?? null;
				$offset = $offset ?? $this->offset ?? 0;

				if ($limit > 0 || $order != NULL) {
					try {
						$qualified_order = [];
						foreach ($order as $of => $o) {
							$fq = $this->getFullyQualifiedField($of);
							$qualified_order[$fq] = $o;
						}

						$paginator = new Paginator();
						$paginator->setLimit($limit);
						$paginator->setOffset($offset);
						$paginator->setOrders($qualified_order);
						$paginator->setAttr($this->attributes);
						$paginator->compile();

						$this->pag_vals = $paginator->getBinding();
					} catch (SqlException $e) {
						throw new SqlException("Pagination error: {$e->getMessage()}");
					}
				} else {
					$paginator = null;
				}
			} else {
				$paginator = null;
			}
		}

		$imp = function (array $fields) {
			if (!$this->enable_qualification) {
				return implode(',', $fields);
			}

			$ta = $this->getTableAlias();

			$arr = array_map(function ($f) use ($ta) {
				return "$ta.$f";
			}, $fields);

			return implode(',', $arr);
		};

		if (!$existance) {
			if (!empty($fields)) {
				$_f_imp = $imp($fields);
				$_f     = $_f_imp . ',';
			} else
				$_f = '';

			if ($aggregate_func != null) {
				if (strtoupper($aggregate_func) == 'COUNT') {
					if ($aggregate_field == null)
						$aggregate_field = '*';

					if ($this->distinct)
						$q  = "SELECT $_f $aggregate_func(DISTINCT $aggregate_field)" . (!empty($aggregate_field_alias) ? " as $aggregate_field_alias" : '');
					else
						$q  = "SELECT $_f $aggregate_func($aggregate_field)" . (!empty($aggregate_field_alias) ? " as $aggregate_field_alias" : '');
				} else {
					$q  = "SELECT $_f $aggregate_func($aggregate_field)" . (!empty($aggregate_field_alias) ? " as $aggregate_field_alias" : '');
				}
			} else {
				$sq = 'SELECT ';

				// SELECT RAW
				if (!empty($this->select_raw_q)) {
					$distinct = ($this->distinct == true) ? 'DISTINCT' : '';

					// $other_fields = !empty($fields) ? ', '.$_f_imp : '';
					// $q  .= $distinct .' '.$this->select_raw_q. $other_fields;

					$other_fields = !empty($fields) ? $_f_imp : '';
					$q  = $other_fields;
					$q .= (!empty(trim($q)) ? ',' : '') . $this->select_raw_q;

					$q = "$sq $distinct $q";
				} else {
					if (empty($fields))
						$q  = $sq . '*';
					else {
						$distinct = ($this->distinct == true) ? 'DISTINCT' : '';
						$q  = $sq . $distinct . ' ' . $_f_imp;
					}
				}
			}
		} else {
			$q  = 'SELECT EXISTS (SELECT 1';
		}

		$q  .= ' FROM ' . DB::quote($this->from());

		////////////////////////
		$values = array_merge($this->w_vals, $this->h_vals);
		$vars   = array_merge($this->w_vars, $this->h_vars);
		////////////////////////


		// Validación
		if (!empty($this->validator)) {
			$validado = $this->validator->validate(array_combine($vars, $values), $this->getRules());

			if ($validado !== true) {
				throw new InvalidValidationException(json_encode(
					$this->validator->getErrors()
				));
			}
		}

		// JOINS
		$joins = '';
		foreach ($this->joins as $j) {
			if ($j[4] == 'CROSS JOIN' || $j[4] == 'NATURAL JOIN') {
				$joins .= " $j[4] $j[0] ";
			} else {
				$joins .= " $j[4] $j[0] ON $j[1]$j[2]$j[3] ";
			}
		}

		$joins .= ' ' . implode(' ', $this->join_raw);

		$q  .= $joins;


		// WHERE
		$where_section = $this->whereFormedQuery();
		if (!empty($where_section)) {

			// patch
			$where_section = str_replace(
				[
					'AND OR',
					'(AND ',
					'(OR '
				],
				[
					'OR ',
					'( ',
					'( '
				],
				$where_section
			);

			$where_section = str_replace('(  NOT ', '(NOT ', $where_section);

			$q  .= ' WHERE ' . $where_section;
		}


		$group = (!empty($this->group)) ? 'GROUP BY ' .  $imp($this->group) : '';
		$q  .= " $group";


		// HAVING
		$having_section = $this->havingFormedQuery();

		if (!empty($having_section)) {

			// patch
			$having_section = str_replace(
				[
					'AND OR',
					'(AND ',
					'(OR '
				],
				[
					'OR ',
					'( ',
					'( '
				],
				$having_section
			);

			$having_section = str_replace('(  NOT ', '(NOT ', $having_section);

			$q  .= ' HAVING ' . $having_section;
		}

		if ($this->randomize) {
			$q .= DB::random();
		} else {
			if (!empty($this->raw_order))
				$q .= ' ORDER BY ' . implode(', ', $this->raw_order);
		}

		// UNION
		if (!empty($this->union_q)) {
			$q .= 'UNION ' . ($this->union_type == 'ALL' ? 'ALL' : '') . ' ' . $this->union_q . ' ';
		}


		$q = rtrim($q);
		$q = Strings::rTrim('AND', $q);
		$q = Strings::rTrim('OR',  $q);


		// PAGINATION
		if (!$existance && $paginator !== null) {
			$q .= $paginator->getQuery();
		}

		$q  = rtrim($q);

		if ($existance)
			$q .= ')';


		if (isset($this->table_alias[$this->table_name])) {
			$tb_name = $this->table_alias[$this->table_name];
		} else {
			$tb_name = $this->table_name;
		}

		$q = preg_replace_callback('/ \.([a-z0-9_]+)/', function ($matches) use ($tb_name) {
			return ' ' . $tb_name . '.' .  $matches[1];
		}, $q);

		$q = preg_replace_callback('/\(\.([a-z0-9_]+)/', function ($matches) use ($tb_name) {
			return '(' . $tb_name . '.' .  $matches[1];
		}, $q);


		$q = str_replace('WHERE AND', 'WHERE', $q);
		$q = str_replace('AND AND', 'AND',  $q);


		$this->last_bindings = $this->getBindings();
		$this->last_pre_compiled_query = $q;
		$this->last_operation = 'get';

		return $q;
	}

	function whereFormedQuery()
	{
		$where = $this->where_raw_q . ' ';

		if (!empty($this->where)) {
			$implode = '';

			$cnt = count($this->where);

			if ($cnt > 0) {
				$implode .= $this->where[0];
				for ($ix = 1; $ix < $cnt; $ix++) {
					$implode .= ' ' . $this->where_group_op[$ix] . ' ' . $this->where[$ix];
				}
			}

			$where = trim($where);

			if (!empty($where)) {
				$op = $this->where_group_op[0] ?? 'AND';
				$where = "($where) $op " . $implode . ' '; // <-------------
			} else {
				$where = "$implode ";
			}
		}

		$where = trim($where);

		if ($this->inSchema([$this->deletedAt])) {
			if (!$this->show_deleted) {

				$tb_name   = $this->getTableAlias();
				$deletedAt = $this->enable_qualification ? "{$tb_name}.{$this->deletedAt}" : $this->deletedAt;

				if (empty($where))
					$where = "$deletedAt IS NULL";
				else
					$where =  ($where[0] == '(' && $where[strlen($where) - 1] == ')' ? $where :   "($where)") . " AND $deletedAt IS NULL";
			}
		}

		return ltrim($where);
	}

	function havingFormedQuery()
	{
		$having = '';

		if (!empty($this->having_raw_q))
			$having = $this->having_raw_q . ' ';

		if (!empty($this->having)) {
			$implode = '';

			$cnt = count($this->having);

			if ($cnt > 0) {
				$implode .= $this->having[0];
				for ($ix = 1; $ix < $cnt; $ix++) {
					$implode .= ' ' . $this->having_group_op[$ix] . ' ' . $this->having[$ix];
				}
			}

			$having = trim($having);

			if (!empty($having)) {
				$having = "($having) AND " . $implode . ' ';
			} else {
				$having = "$implode ";
			}
		}

		// acá viene la magia
		$having = preg_replace_callback('/([a-z]+)\(([a-z0-9_]+)\)/i', function ($matches) {
			$fn    = $matches[1];
			$field = $matches[2];

			$field = $this->getFullyQualifiedField($field);

			return "$fn($field)";
		}, $having);

		return trim($having);
	}

	function getBindings()
	{
		$pag = [];
		if (!empty($this->pag_vals)) {
			switch (count($this->pag_vals)) {
				case 2:
					$pag = [$this->pag_vals[0][1], $this->pag_vals[1][1]];
					break;
				case 1:
					$pag = [$this->pag_vals[0][1]];
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
	function mergeBindings(Model $model)
	{
		$this->to_merge_bindings = $model->getBindings();

		if (!empty($this->table_raw_q)) {
			$this->from_raw_vals = $this->to_merge_bindings;
		}

		return $this;
	}

	/*
		 https://www.php.net/manual/en/pdo.constants.php

	*/
	protected function bind(string $q)
	{
		if (!$this->bind) {
			return;
		}

		if ($this->conn == null) {
			$this->connect();
		}

		$vals = array_merge(
			$this->select_raw_vals,
			$this->from_raw_vals,
			$this->where_raw_vals,
			$this->w_vals,
			$this->having_raw_vals,
			$this->h_vals,
			$this->union_vals
		);

		///////////////[ BUG FIXES ]/////////////////

		$_vals = [];
		$reps  = 0;
		foreach ($vals as $ix => $val) {
			if ($val === NULL) {
				$q = Strings::replaceNth('?', 'NULL', $q, $ix + 1 - $reps);
				$reps++;

				/*
				Corrección para operaciones entre enteros y floats en PGSQL
			*/
			} elseif (DB::driver() == DB::PGSQL && is_float($val)) {
				$q = Strings::replaceNth('?', 'CAST(? AS DOUBLE PRECISION)', $q, $ix + 1 - $reps);
				$reps++;
				$_vals[] = $val;
			} else {
				$_vals[] = $val;
			}
		}

		$vals = $_vals;

		///////////////////////////////////////////

		if (!$this->exec) {
			return; //
		}

		/*
			La idea es poder accederlo desde el Error Handler por ejemplo
			en caso de ser necesario

			Se supone que cualquier SQL generado y ejecutado sera bindeado
			asi que debe de pasar por este lugar 
			
			(excepto claro que se use la clase DB directamente)
		*/

		static::$current_sql = $q;

		try {
			$st = $this->conn->prepare($q);
		} catch (\Exception $e) {
			$vals_str = implode(',', $vals);

			$this->logSQL();
			throw new SqlException("Query '$q' - and vals = [$vals_str] | " . $e->getMessage());
		}

		foreach ($vals as $ix => $val) {
			$type = $this->getParamType($val);
			$st->bindValue($ix + 1, $val, $type);
			//echo "Bind: ".($ix+1)." - $val ($type)\n";
		}

		$sh = count($vals);

		$bindings = $this->pag_vals;
		foreach ($bindings as $ix => $binding) {
			$st->bindValue($ix + 1 + $sh, $binding[1], $binding[2]);
		}

		return $st;
	}

	function getLastPrecompiledQuery()
	{
		return $this->last_pre_compiled_query;
	}

	function getLastBindingParamters()
	{
		return $this->last_bindings;
	}

	private function _dd($pre_compiled_sql, $bindings)
	{
		foreach ($bindings as $ix => $val) {
			// var_dump($val);
			// var_dump(is_string($val));

			if (is_null($val)) {
				$bindings[$ix] = 'NULL';
			} elseif (isset($vars[$ix]) && isset($this->schema['attr_types'][$val])) {
				$const = $this->schema['attr_types'][$val];

				if ($const == 'STR')
					$bindings[$ix] = "'$val'";
			} elseif (is_int($val)) {
				// pass
			} elseif (is_bool($val)) {
				// pass
			} elseif (is_string($val)) {
				$bindings[$ix] = "'$val'";
			}
		}

		$sql = Arrays::strReplace('?', $bindings, $pre_compiled_sql);
		$sql = trim(preg_replace('!\s+!', ' ', $sql));

		if ($this->semicolon_ending) {
			$sql .= ';';
		}

		if ($this->sql_formatter_status) {
			$sql = static::sqlFormatter($sql);
		}

		return $sql;
	}

	// Debug query
	function dd(bool $sql_formatter = false)
	{
		$this->sql_formatter_status = self::$sql_formatter_status ?? $sql_formatter;

		if ($this->last_operation == 'create') {
			return $this->last_compiled_sql;
		}

		return $this->_dd($this->toSql(), $this->getBindings());
	}

	function getLog(bool $sql_formatter = false)
	{
		$this->sql_formatter_status = self::$sql_formatter_status ?? $sql_formatter;

		if ($this->last_operation == 'create') {
			return $this->last_compiled_sql;
		}

		return $this->_dd($this->last_pre_compiled_query, $this->last_bindings);
	}

	function debug()
	{
		$op = $this->current_operation ?? $this->last_operation;

		if ($op == 'create') {
			$combined = array_combine($this->insert_vars, $this->getLastBindingParamters());
			$sql = $this->last_pre_compiled_query;

			return preg_replace_callback('/:([a-z][a-z0-9_\-ñáéíóú]+)/', function ($matches) use ($combined) {
				$key = $matches[1];

				// para el debug ignoro los tipos
				return "'$combined[$key]'";
			}, $sql);
		} else {
			return $this->dd();
		}
	}

	function logSQL()
	{
		$config = Config::get();

		if ($config['debug'] && $config['log_sql']) {
			log_sql($this->dd() ?? 'EMPTY-QUERY');
		}
	}

	function getLastOperation()
	{
		return $this->last_operation;
	}

	function getCurrentOperation()
	{
		return $this->current_operation;
	}

	function getOp()
	{
		return $this->current_operation ?? $this->last_operation;
	}

	function getWhere()
	{
		return $this->where;
	}

	public function connectTo(array $tables)
	{
		// Habilitar la calificación automáticamente cuando se usa connectTo
		$this->qualify();

		$this->connect_to = $tables;
		return $this;
	}

	/**
	 * Check if a column exists in the table schema.
	 *
	 * @param string $column The name of the column to check.
	 * @return bool True if the column exists, false otherwise.
	 */
	protected function columnExists(string $column): bool
	{
		$column = $this->unqualifyField($column); // Handle 'table.column' format

		if ($this->schema === null || empty($this->attributes)) {
			return false;
		}
		return in_array($column, $this->attributes, true);
	}

	/**
	 * Procesa la condición WHERE para manejar calificadores de tablas relacionadas
	 * 
	 * @param array|string $condition La condición a procesar
	 * @return array La condición procesada lista para usar
	 */
	protected function processWhereCondition($condition)
	{
		// Si no está habilitada la calificación o la condición no es un array, retornar la condición original
		if (!isset($this->enable_qualification) || !$this->enable_qualification || !is_array($condition)) {
			return $condition;
		}

		// Verificar si la condición tiene formato de columna calificada (tabla.columna)
		if (isset($condition[0]) && is_string($condition[0]) && strpos($condition[0], '.') !== false) {
			list($relatedTable, $relatedColumn) = explode('.', $condition[0], 2);

			// Verificar si esta tabla relacionada ya está en joins
			$relatedTableExists = false;
			foreach ($this->joins as $join) {
				if ($join[0] === $relatedTable) {
					$relatedTableExists = true;
					break;
				}
			}

			// Si la tabla relacionada no está en joins, intentar añadirla
			if (!$relatedTableExists) {
				// Buscar relación en el schema
				if (isset($this->schema) && isset($this->schema['expanded_relationships'][$relatedTable])) {
					$relation = $this->schema['expanded_relationships'][$relatedTable][0];
					$this->join($relatedTable);
					$relatedTableExists = true;
				} else {
					// Verificar si es un alias derivado de una FK (ej: 'professor' → 'professor_id')
					$possibleFk = $relatedTable . '_id';
					if ($this->columnExists($possibleFk)) {
						// Es un alias de una FK directa, necesitamos hacer join con la tabla correcta
						$this->join($this->findTableByAlias($relatedTable), 'id', '=', $this->table_name . '.' . $possibleFk);
						$relatedTableExists = true;
					}
				}
			}

			// Si logramos añadir la tabla relacionada, modificar la condición
			if ($relatedTableExists) {
				// La condición ahora usa la columna calificada
				$condition[0] = "$relatedTable.$relatedColumn";
			}
		}

		return $condition;
	}

	/**
	 * Encuentra la tabla real correspondiente a un alias derivado
	 * 
	 * @param string $alias El alias a buscar
	 * @return string|null La tabla correspondiente o null si no se encuentra
	 */
	public function findTableByAlias($alias)
	{
		// Buscar en todas las relaciones para encontrar de qué tabla proviene este alias
		if (isset($this->schema) && isset($this->schema['expanded_relationships'])) {
			foreach ($this->schema['expanded_relationships'] as $tableName => $relations) {
				foreach ($relations as $relation) {
					$sourceField = $relation[1][1]; // ej: professor_id

					// Si el nombre de la columna sugiere un alias
					if (preg_match('/^(.+)_id$/', $sourceField, $matches)) {
						$possibleAlias = $matches[1];
						if ($possibleAlias === $alias) {
							return $tableName;
						}
					}
				}
			}
		}

		// Si no encontramos correspondencia, asumimos que el alias es igual al nombre de la tabla
		return $alias;
	}

	function get(array $fields = null, array $order = null, int $limit = NULL, int $offset = null, $pristine = false)
	{
		$this->onReading();

		// Si hay tablas conectadas
		if (!empty($this->connect_to)) {
			$output = $this->getSubResources(
				$this->table_name,
				$this->connect_to,
				$this,
				DB::getCurrentConnectionId()
			);

			return $output;
		}

		// Flujo normal sin relaciones
		$q  = $this->toSql($fields, $order, $limit, $offset);
		$st = $this->bind($q);

		$count = null;
		if ($this->exec && $st->execute()) {
			$output = $st->fetchAll($this->getFetchMode());

			$count  = $st->rowCount();
			if (empty($output)) {
				$ret = [];
			} else {
				$ret = $pristine ? $output : $this->applyTransformer($this->applyOutputMutators($output));
			}

			$this->onRead($count);
		} else {
			$ret = false;
		}

		return $ret;
	}

	function first(array $fields = null, $pristine = false)
	{
		$this->onReading();

		// Si hay tablas conectadas
		if (!empty($this->connect_to)) {
			$output = $this
				->limit(1)
				->getSubResources(
					$this->table_name,
					$this->connect_to,
					$this,
					DB::getCurrentConnectionId()
				);

			return $output;
		}

		$q  = $this->toSql($fields, NULL);
		$st = $this->bind($q);

		$count = null;
		if ($this->exec && $st->execute()) {
			$output = $st->fetch($this->getFetchMode());
			$count = $st->rowCount();

			if (empty($output)) {
				$ret = []; // deberia retornar null !!!!!! Corregir pero analizar y probar luego ApiController
			} else {
				$ret = $pristine ? $output : $this->applyTransformer($this->applyOutputMutators($output));
			}

			$this->onRead($count);
		} else
			$ret = false;

		return $ret;
	}

	function firstOrFail(array $fields = null, $pristine = false)
	{
		$ret = $this->first($fields, $pristine);

		if (empty($ret)) {
			// Debería ser una Excepción personalizada
			throw new \Exception("No rows");
		}

		return $ret;
	}

	function getOne(array $fields = null, $pristine = false)
	{
		return $this->first($fields, $pristine);
	}

	function top(array $fields = null, $pristine = false)
	{
		return $this->first($fields, $pristine);
	}

	function value($field, ?string $cast_to = null)
	{
		$this->onReading();

		$q  = $this->toSql([$field]);
		$st = $this->bind($q);

		$count = null;
		if ($this->exec && $st->execute()) {
			$ret = $st->fetch(\PDO::FETCH_NUM)[0] ?? false;

			$count = $st->rowCount();
			$this->onRead($count);
		} else
			$ret = false;

		/* 
			Castear false daria muchos errores
			ya que es el valor devuelto ante fallo
		*/
		if ($ret != false && !empty($cast_to)) {
			switch ($cast_to) {
				case 'string':
					$ret = (string) $ret;
					break;
				case 'int':
				case 'integer':
					$ret = (int) $ret;
					break;
				case 'float':
					$ret = (float) $ret;
					break;
				case 'double':
					$ret = (float) $ret;
					break;
				case 'bool':
					switch (gettype($ret)) {
						case 'string':
							$_s = strtolower($ret);

							switch ($_s) {
								case '1':
								case 'on':
									$ret = true;
									break;
								case '0':
								case 'off':
									$ret = false;
									break;
							}
							break;

						case 'int':
							switch ($ret) {
								case 1:
									$ret = true;
									break;
								case 0:
									$ret = false;
									break;
							}
							break;
					}
					// sino cumple esas reglas estricas, el casting de bools no se efectua

					break;
				default:
					throw new \InvalidArgumentException("Invalid cast");
			}
		}

		return $ret;
	}

	function exists()
	{
		$q  = $this->toSql(null, null, null, null, true);
		$st = $this->bind($q);

		if ($this->exec && $st->execute()) {
			return (bool) $st->fetch(\PDO::FETCH_NUM)[0];
		} else
			return false;
	}

	function pluck(string $field)
	{
		$this->setFetchMode('COLUMN');
		$this->fields = [$field];

		$q  = $this->toSql();
		$st = $this->bind($q);

		if ($this->exec && $st->execute()) {
			$res = $this->applyTransformer(
				$this->applyOutputMutators(
					$st->fetchAll($this->getFetchMode())
				)
			);

			/*	
				Si el schema tiene algo como:

				'sql_data_types' => [
					'{campo}' => 'JSON',
					// ...
				],
			*/
			if ($this->schema != NULL && isset($this->schema['sql_data_types']) && isset($this->schema['sql_data_types'][$field])) {
				if ($this->schema['sql_data_types'][$field] == 'JSON') {
					$res = array_map(
						function ($e) {
							return json_decode($e, true);
						},
						$res
					);
				}
			}

			return $res;
		} else
			return false;
	}

	protected function aggregate(string $fn_name, $field, $alias)
	{
		$fn_name = strtoupper($fn_name);

		if (!in_array($fn_name, [
			'MAX',
			'MIN',
			'COUNT',
			'SUM',
			'AVG'
		])) {
			throw new \InvalidArgumentException("Invalid aggregate function");
		}

		$q = $this->toSql(null, null, null, null, false, $fn_name, $field, $alias);
		$st = $this->bind($q);

		if (empty($this->group)) {
			if ($this->exec && $st->execute()) {
				// fetch
				return (int) $st->fetch($this->getFetchMode('COLUMN'));
			} else
				return false;
		} else {
			if ($this->exec && $st->execute()) {
				// fetch all
				return (int) $st->fetchAll($this->getFetchMode('COLUMN'));
			} else
				return false;
		}
	}

	function avg($field, $alias = NULL)
	{
		return $this->aggregate(__FUNCTION__, $field, $alias);
	}

	function sum($field, $alias = NULL)
	{
		return $this->aggregate(__FUNCTION__, $field, $alias);
	}

	function min($field, $alias = NULL)
	{
		return $this->aggregate(__FUNCTION__, $field, $alias);
	}

	function max($field, $alias = NULL)
	{
		return $this->aggregate(__FUNCTION__, $field, $alias);
	}

	function count($field = NULL, $alias = NULL)
	{
		return $this->aggregate(__FUNCTION__, $field, $alias);
	}

	function getWhereVals(): array
	{
		return $this->w_vals;
	}

	function getWhereVars(): array
	{
		return $this->w_vars;
	}

	function getWhereRawVals()
	{
		return $this->where_raw_vals;
	}

	function getHavingVals(): array
	{
		return $this->h_vals;
	}

	function getHavingVars(): array
	{
		return $this->h_vars;
	}

	function getHavingRawVals(): array
	{
		return $this->having_raw_vals;
	}

	// crea un grupo dentro del where
	function group(callable $closure, string $conjunction = 'AND', bool $negate = false)
	{
		$not = $negate ? ' NOT ' : '';

		$m = new Model();
		call_user_func($closure, $m);

		$w_formed 	= $m->whereFormedQuery();

		if (!empty($w_formed)) {
			$w_vars   	= $m->getWhereVars();
			$w_vals   	= $m->getWhereVals();
			$w_raw_vals = $m->getWhereRawVals();

			$this->where[] = "$conjunction $not($w_formed)";
			$this->w_vars  = array_merge($this->w_vars, $w_vars);
			$this->w_vals  = array_merge($this->w_vals, $w_raw_vals, $w_vals); // *

			$this->where_group_op[] = '';
		}


		$h_formed 	= $m->havingFormedQuery();

		if (!empty($h_formed)) {
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

	function and(callable $closure)
	{
		return $this->group($closure, 'AND', false);
	}

	function or(callable $closure)
	{
		return $this->group($closure, 'OR', false);
	}

	function andNot(callable $closure)
	{
		return $this->group($closure, 'AND', true);
	}

	// alias
	function not(callable $closure)
	{
		return $this->andNot($closure);
	}

	function orNot(callable $closure)
	{
		return $this->group($closure, 'OR', true);
	}

	function when($precondition = null, ?callable $closure = null, ?callable $closure2 = null)
	{
		if (!empty($precondition)) {
			call_user_func($closure, $this);
		} elseif ($closure2 != null) {
			call_user_func($closure2, $this);
		}

		return $this;
	}

	static protected function _where_array(array $cond_ay, $parent_conj = 'AND')
	{
		$accepted_conj = [
			'AND',
			'OR',
			'NOT',
			'AND NOT',
			'OR NOT'
		];

		$code = '';
		foreach ($cond_ay as $key => $ay) {
			if (!is_array($ay)) {
				continue;
			}

			$ay_str = var_export($ay, true);

			//dd($ay, "PARENT CONJ is $parent_conj");

			if (is_string($key)) {
				if (!in_array($key, $accepted_conj)) {
					throw new \Exception("Conjuntion '$key' is invalid");
				}

				$conj = $key;

				$is_simple = Arrays::areSimpleAllSubArrays($ay);
				$is_multi  = is_array($ay) && Arrays::isMultidim($ay);

				//dd($ay, "GRUPO con op $key " . ($is_simple ? ' -- simple' : ''));

				if ($is_simple || !$is_multi) {
					$w_type = ($parent_conj == 'OR' ? 'whereOr' : 'where');

					// switch ($parent_conj){
					// 	case 'OR':
					// 		$w_type = 'whereOr';
					// 		break;
					// 	case 'AND':
					// 		$w_type = 'where';
					// 		break;

					// 	case 'NOT':
					// 		$w_type = 'andNot'; //
					// 		break;
					// 	case 'OR NOT':
					// 		$w_type = 'orNot'; //
					// 		break;
					// 	case 'AND NOT':
					// 		$w_type = 'andNot'; //
					// 		break;

					// 	default:
					// 		$w_type = 'where';
					// 		break;
					// }


					$code .= "\$q->$w_type($ay_str);\n";
				} else {
					$code  .=  "\$q->group(function (\$q) {" . static::_where_array($ay, $conj) . "});\n";
				}
			} else {
				$is_simple = is_array($ay) && Arrays::areSimpleAllSubArrays($ay);
				$is_multi  = is_array($ay) && Arrays::isMultidim($ay);

				//dd($ay, "GRUPO - key $key" . ($is_simple ? ' -- simple' : ''));

				if ($is_simple || !$is_multi) {

					$w_type = ($parent_conj == 'OR' ? 'orWhere' : 'where');

					// switch ($parent_conj){
					// 	case 'OR':
					// 		$w_type = 'orWhere';
					// 		break;
					// 	case 'AND':
					// 		$w_type = 'where';
					// 		break;

					// 	case 'NOT':
					// 		$w_type = 'andNot'; //
					// 		break;
					// 	case 'OR NOT':
					// 		$w_type = 'orNot'; //
					// 		break;
					// 	case 'AND NOT':
					// 		$w_type = 'andNot'; //
					// 		break;

					// 	default:
					// 		$w_type = 'where';
					// 		break;
					// }


					$code  .= "\$q->$w_type(" . $ay_str . ");\n";
				} else {

					// dd(
					// 	$ay, "Multi?" . ((int) $is_multi) .  " Simple? " . ((int) $is_simple)
					// );

					if (!in_array($key, $accepted_conj)) {
						$conj = 'AND';
					} else {
						$conj = $key;
					}

					$code  .=  "\$q->group(function (\$q) {" . static::_where_array($ay, $conj) . "});\n";
				}
			}
		}

		return $code;
	}

	/*
		Interpreta un array como el siguiente:

      	[
			'AND' => [
				[
					'OR' => [
						'OR' => [
							['cost', 100, '<='],
							['description', NULL, 'IS NOT']
						],

						['name', '%Pablo', 'LIKE']
					]
				],

				['stars', 5]
			]    
		]

		Y debe poder interpretar lo siguiente:

		[
			'AND' => [
				['name', '%a%', 'LIKE'],

				[
					'AND' => [                     // <----- podria ser hibrido funcionando igual si falta la conjuncion y asumiendo es 'AND'
						['cost', 100, '>'],
						['id', 50, '<']
					]
				],
				
				[
					'OR' => [
						['is_active', 1],
						[
							'AND' => [ 
								['cost', 100, '<='],
								['description', NULL, 'IS NOT']
							]
						]
					]
				],
				
				['belongs_to', 150, '>']		
			]	
		]

		* Tambien deberia poder (a futuro) aceptar NOT, AND NOT y OR NOT

		De momento devuelve el codigo que debe evaluarse con eval()

		Ej:

		$q = Model::where_array($ay);

        $code = Strings::beforeLast("return table('products')$q", ';') . '->dd();';

        dd($code);

        DB::getConnection("az");
        
        dd(
            eval($code)
        );

		Podria eventualmente armar solamente el where() y encolar los parametros para hacer luego el binding sin requerir eval.

		Antes llamada where_ay()
	*/
	static function where_array(array $cond_ay)
	{
		return ltrim(
			static::_where_array($cond_ay),
			'$q'
		);
	}

	protected function _where(?array $conditions = null, string $group_op = 'AND', $conjunction = null)
	{
		//dd($group_op, 'group_op');
		//dd($conjunction, 'conjuntion');

		if (empty($conditions)) {
			return;
		}

		// Procesar condiciones para manejar calificadores de tabla
		$conditions = $this->processWhereCondition($conditions);

		if (Arrays::isAssoc($conditions)) {
			$conditions = Arrays::nonAssoc($conditions);
		}

		if (isset($conditions[0]) && is_string($conditions[0]))
			$conditions = [$conditions];

		$_where = [];
		$vars   = [];
		$ops    = [];

		if (count($conditions) > 0) {
			if (is_array($conditions[Arrays::arrayKeyFirst($conditions)])) {

				foreach ($conditions as $ix => $cond) {
					$unqualified_field = $this->unqualifyField($cond[0]);
					$field             = $this->getFullyQualifiedField($cond[0]);
					$field     	       = $this->getWrapFieldName($field); // <--- wrap

					if ($field == null)
						throw new SqlException("Field can not be NULL");

					if (is_array($cond[1]) && (empty($cond[2]) || in_array($cond[2], ['IN', 'NOT IN']))) {
						// Determinar si se deben comillar los valores
						$should_quote = true;

						if ($this->isDecimalField($unqualified_field) && !$this->shouldQuoteDecimals()) {
							$should_quote = false;
						} else if (isset($this->schema['attr_types'][$unqualified_field])) {
							$should_quote = $this->schema['attr_types'][$unqualified_field] == 'STR';
						}

						// Aplicar comillas si corresponde
						if ($should_quote) {
							$cond[1] = array_map(function ($e) {
								return "'$e'";
							}, $cond[1]);
						}

						$in_val = implode(', ', $cond[1]);

						$op = isset($cond[2]) ? $cond[2] : 'IN';
						$_where[] = "$field $op ($in_val) ";
					} else {
						$vars[] = $field;
						$this->w_vals[] = $cond[1];

						if ($cond[1] === NULL && (empty($cond[2]) || $cond[2] == '='))
							$ops[] = 'IS';
						else
							$ops[] = $cond[2] ?? '=';
					}
				}
			} else {
				$vars[]         = $conditions[0];
				$this->w_vals[] = $conditions[1];

				if ($conditions[1] === NULL && (empty($conditions[2]) || $conditions[2] == '='))
					$ops[] = 'IS';
				else
					$ops[] = $conditions[2] ?? '=';
			}
		}

		foreach ($vars as $ix => $var) {
			$_where[] = "$var $ops[$ix] ?";
		}

		$this->w_vars = array_merge($this->w_vars, $vars); //

		////////////////////////////////////////////
		// group
		$ws_str = implode(" $conjunction ", $_where);

		if (count($conditions) > 1 && !empty($ws_str))
			$ws_str = "($ws_str)";

		$this->where_group_op[] = $group_op;

		$this->where[] = ' ' . $ws_str;

		return;
	}

	function whereColumn(string $field1, string $field2, string $op = '=')
	{
		$validation = Factory::validador()->validate(
			[
				'col1' => $field1,
				'col2' => $field2
			],
			[
				'col1' => ['type' => 'alpha_num_dash'],
				'col2' => ['type' => 'alpha_num_dash']
			]
		);

		if (!$validation) {
			throw new InvalidValidationException(json_encode(
				$this->validator->getErrors()
			));
		}

		if (!in_array($op, ['=', '>', '<', '<=', '>=', '!='])) {
			throw new \InvalidArgumentException("Invalid operator '$op'");
		}

		$field1 = $this->getFullyQualifiedField($field1);
		$field2 = $this->getFullyQualifiedField($field2);

		$this->where_raw_q = "{$field1}{$op}{$field2}";
		return $this;
	}

	// function where($conditions, $conjunction = 'AND')
	// {
	// 	// Si se hace where(1) lo convierte en WHERE 1=1
	// 	if ($conditions == 1){
	// 		$conditions = [1, 1];
	// 	}

	// 	/*
	// 		Laravel compatibility

	// 		In "Laravel mode", $conditions es la key y $conjunction el valor
	// 	*/
	// 	if (is_string($conditions)){
	// 		$key              = $conditions;
	// 		$conditions       = [];
	// 		$conditions[$key] = $conjunction;
	// 	}

	// 	$this->_where($conditions, 'AND', $conjunction);
	// 	return $this;
	// }

	/*
		Refactoring por Claude para compatibilidad con Laravel

		https://claude.ai/chat/3331ae1f-5190-4789-b27a-8faa347b1973
	*/
	function where(...$args)
	{
		// Caso actual: where($conditions, $conjunction = 'AND')
		if (is_array($args[0])) {
			$conditions = $args[0];
			$conjunction = $args[1] ?? 'AND';
			$this->_where($conditions, 'AND', $conjunction);
			return $this;
		}

		// Caso Laravel: where($field, $operator, $value) o where($field, $value)
		if (count($args) == 2) {
			// where('field', 'value')
			$field = $args[0];
			$value = $args[1];
			$operator = '=';
		} else if (count($args) == 3) {
			// where('field', '>', 'value')
			$field = $args[0];
			$operator = $args[1];
			$value = $args[2];
		} else {
			throw new \InvalidArgumentException("Invalid number of arguments for where()");
		}

		// Convertimos al formato actual
		$this->_where([[$this->getFullyQualifiedField($field), $value, $operator]], 'AND', 'AND');
		return $this;
	}

	function orWhere($conditions, $conjunction = 'AND')
	{
		$this->_where($conditions, 'OR', $conjunction);
		return $this;
	}

	/*
		Sin ensayar

		Ej:

		DB::table('contacts')
		->whereLike('full_name', "%$search%")
		->orWhereLike('company', "%$search%")
		->orWhereLike('email', "%$search%")
		->get();
	*/
	function orWhereLike(string $field, $val)
	{
		$this->_where([[$this->getFullyQualifiedField($field), $val, 'LIKE']], 'OR');
		return $this;
	}

	function whereOr($conditions)
	{
		$this->_where($conditions, 'AND', 'OR');
		return $this;
	}

	// ok
	function orHaving($conditions, $conjunction = 'AND')
	{
		$this->_having($conditions, 'OR', $conjunction);
		return $this;
	}

	function orWhereRaw(string $q, array $vals = null)
	{
		$this->or(function ($x) use ($q, $vals) {
			$x->whereRaw($q, $vals);
		});

		return $this;
	}

	/*
		Es un error usar or() ya que depende de group() que afecta solo a los WHERE
	*/
	function orHavingRaw(string $q, array $vals = null)
	{
		// $this->or(function($x) use ($q, $vals){
		// 	$x->HavingRaw($q, $vals);
		// });

		// return $this;
	}

	function firstWhere($conditions, $conjunction = 'AND')
	{
		$this->where($conditions, $conjunction);
		return $this->first();
	}

	function find($id)
	{
		if (empty($this->schema)) {
			return $this->where(['id' => $id]);
		}

		return $this->where([$this->getFullyQualifiedField($this->schema['id_name']) => $id]);
	}

	/*
		In Laravel,	works with Relationships
		
		The method also works with HasMany, HasManyThrough, BelongsToMany, MorphMany, and MorphToMany relations seamlessly.

		$user->posts()->findOr(1, fn () => '...');
	*/
	function findOr($id, ?callable $fn = null)
	{
		$query = $this->find($id);

		if ($fn != null && !$this->exists()) {
			return $fn($id);
		}

		return $query;
	}

	function findOrFail($id)
	{
		return $this->findOr($id, function ($id) {
			throw new \Exception("Resource for `{$this->table_name}` and id=$id doesn't exist");
		});
	}

	function whereNot(string $field, $val)
	{
		$this->where([$this->getFullyQualifiedField($field), $val, '!=']);
		return $this;
	}

	function whereNull(string $field)
	{
		$this->where([$this->getFullyQualifiedField($field), NULL]);
		return $this;
	}

	function whereNotNull(string $field)
	{
		$this->where([$this->getFullyQualifiedField($field), NULL, 'IS NOT']);
		return $this;
	}

	function whereIn(string $field, array $vals)
	{
		$this->where([$this->getFullyQualifiedField($field), $vals, 'IN']);
		return $this;
	}

	function whereNotIn(string $field, array $vals)
	{
		$this->where([$this->getFullyQualifiedField($field), $vals, 'NOT IN']);
		return $this;
	}

	function whereBetween(string $field, array $vals)
	{
		if (count($vals) != 2)
			throw new \InvalidArgumentException("whereBetween accepts an array of exactly two items");

		$min = min($vals[0], $vals[1]);
		$max = max($vals[0], $vals[1]);

		$this->where([$this->getFullyQualifiedField($field), $min, '>=']);
		$this->where([$this->getFullyQualifiedField($field), $max, '<=']);
		return $this;
	}

	function whereNotBetween(string $field, array $vals)
	{
		if (count($vals) != 2)
			throw new \InvalidArgumentException("whereBetween accepts an array of exactly two items");

		$min = min($vals[0], $vals[1]);
		$max = max($vals[0], $vals[1]);

		$this->where([
			[$this->getFullyQualifiedField($field), $min, '<'],
			[$this->getFullyQualifiedField($field), $max, '>']
		], 'OR');
		return $this;
	}

	function whereLike(string $field, $val)
	{
		$this->where([$this->getFullyQualifiedField($field), $val, 'LIKE']);
		return $this;
	}

	function oldest()
	{
		$this->orderBy([$this->getFullyQualifiedField($this->createdAt) => 'DESC']);
		return $this;
	}

	function latest()
	{
		$this->oldest();
		return $this;
	}

	function newest()
	{
		$this->orderBy([$this->getFullyQualifiedField($this->createdAt) => 'ASC']);
		return $this;
	}

	function _having(array $conditions = null, $group_op = 'AND', $conjunction = null)
	{
		if (Arrays::isAssoc($conditions)) {
			$conditions = Arrays::nonAssoc($conditions);
		}

		if ((count($conditions) == 3 || count($conditions) == 2) && !is_array($conditions[1]))
			$conditions = [$conditions];

		// dd($conditions, 'CONDITIONS');

		$_having = [];
		foreach ((array) $conditions as $cond) {
			if (Arrays::isAssoc($cond)) {
				$cond[0] = Arrays::arrayKeyFirst($cond);
				$cond[1] = $cond[$cond[0]];
			}

			if (in_array($cond[0], $this->getAttr())) {
				$dom = $this->getFullyQualifiedField($cond[0]);
			} else {
				$dom = $cond[0];
			}

			$op = $cond[2] ?? '=';

			$_having[] = "$dom $op ?";
			$this->h_vars[] = $dom;
			$this->h_vals[] = $cond[1];
		}

		////////////////////////////////////////////
		// group
		$ws_str = implode(" $conjunction ", $_having);

		if (count($conditions) > 1 && !empty($ws_str))
			$ws_str = "($ws_str)";

		$this->having_group_op[] = $group_op;

		$this->having[] = ' ' . $ws_str;
		////////////////////////////////////////////

		// dd($this->having, 'HAVING:');
		// dd($this->h_vars, 'VARS');
		// dd($this->h_vals, 'VALUES');

		return $this;
	}

	/*
		Implementacion completada por Claude --sin probar

		Ej:

		DB::table('orders')
		->havingRaw('COUNT(id) > ?', [5])
		->havingRaw('SUM(amount) > ?', [1000], 'OR')
		->get();

		// Generaría algo como:
		// ... HAVING (COUNT(id) > ?) OR (SUM(amount) > ?)
	*/
	function havingRaw(string $q, array $vals = null, $conjunction = 'AND')
	{
		if (substr_count($q, '?') != count($vals))
			throw new \InvalidArgumentException("Number of ? are not consistent with the number of passed values");

		if (empty($this->having_raw_q)) {
			$this->having_raw_q = $q;
		} else {
			$this->having_raw_q = "({$this->having_raw_q}) $conjunction ($q)";
		}

		if ($vals != null) {
			if (empty($this->having_raw_vals)) {
				$this->having_raw_vals = $vals;
			} else {
				$this->having_raw_vals = array_merge($this->having_raw_vals, $vals);
			}
		}

		return $this;
	}

	// function having(array $conditions, $conjunction = 'AND')
	// {	
	// 	if (Arrays::isAssoc($conditions)){
	//         $conditions = Arrays::nonAssoc($conditions);
	//     }

	// 	if (!is_array($conditions[0])){
	// 		if (Strings::contains('(', $conditions[0])){
	// 			$op = $conditions[2] ?? '=';

	// 			$q = "{$conditions[0]} {$op} ?";
	// 			$v = $conditions[1];

	// 			if ($this->strict_mode_having){
	// 				throw new \Exception("Use havingRaw() instead for {$q}");
	// 			}

	// 			$this->havingRaw($q, [$v]);
	// 			return $this;
	// 		}
	// 	} 

	// 	$this->_having($conditions, 'AND', $conjunction);
	// 	return $this;
	// }

	/*
		Refactoring por Claude para compatibilidad con Laravel

		https://claude.ai/chat/3331ae1f-5190-4789-b27a-8faa347b1973
	*/
	function having(...$args)
	{
		// Caso actual: having(array $conditions, $conjunction = 'AND')
		if (is_array($args[0])) {
			$conditions  = $args[0];
			$conjunction = $args[1] ?? 'AND';

			// Mantener el comportamiento actual para expresiones tipo COUNT, SUM, etc
			if (isset($conditions[0]) && !is_array($conditions[0])) {
				if (Strings::contains('(', $conditions[0])) {
					$op = $conditions[2] ?? '=';
					$q = "{$conditions[0]} {$op} ?";
					$v = $conditions[1];

					if ($this->strict_mode_having) {
						throw new \Exception("Use havingRaw() instead for {$q}");
					}

					$this->havingRaw($q, [$v]);
					return $this;
				}
			}

			$this->_having($conditions, 'AND', $conjunction);
			return $this;
		}

		// Caso Laravel: having($field, $operator, $value) o having($field, $value)
		if (count($args) == 2) {
			// having('field', 'value')
			$field = $args[0];
			$value = $args[1];
			$operator = '=';
		} else if (count($args) == 3) {
			// having('field', '>', 'value')
			$field = $args[0];
			$operator = $args[1];
			$value = $args[2];
		} else {
			throw new \InvalidArgumentException("Invalid number of arguments for having()");
		}

		// Convertimos al formato actual
		$this->_having([[$this->getFullyQualifiedField($field), $value, $operator]], 'AND', 'AND');
		return $this;
	}

	/*
		No admite eventos. Depredicada.

		El uso de esta función deberá ser reemplazado por DB::select()
	*/
	static function query(string $raw_sql, $fetch_mode = \PDO::FETCH_ASSOC)
	{
		$conn = DB::getConnection();

		$query = $conn->query($raw_sql);
		DB::setRawSql($raw_sql);

		if ($fetch_mode !== null) {
			if (is_string($fetch_mode)) {
				$fetch_mode = constant("\PDO::FETCH_{$fetch_mode}");
			}

			$query->setFetchMode($fetch_mode);
		}

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

		if (empty($data)) {
			throw new SqlException('There is no data to update');
		}

		if (!Arrays::isAssoc($data)) {
			throw new SqlException('Array of data should be associative');
		}

		$this->ignoreFieldsNotPresentInSchema($data);


		$this->data = $data;

		switch ($this->current_operation) {
			case 'restore':
				$this->onRestoring($data);
				break;
			case 'delete':
				$this->onDeleting($data);
			default:
				$this->onUpdating($data);
		}

		$data = $this->applyInputMutator($data, 'UPDATE');
		$vars = array_keys($data);
		$vals = array_values($data);

		if (!empty($this->fillable) && is_array($this->fillable)) {
			foreach ($vars as $var) {
				if (!in_array($var, $this->fillable))
					throw new SqlException("Update: $var is not fillable");
			}
		}

		// Validación
		if (!empty($this->validator)) {
			$validado = $this->validator->validate($data, $this->getRules());
			if ($validado !== true) {
				throw new InvalidValidationException(json_encode(
					$this->validator->getErrors()
				));
			}
		}

		$set = '';
		foreach ($vars as $ix => $var) {
			$set .= " $var = ?, ";
		}
		$set = trim(substr($set, 0, strlen($set) - 2));

		if ($set_updated_at && $this->inSchema([$this->updatedAt])) {
			if (isset($this->config)) {
				$d = new \DateTime('', new \DateTimeZone($this->config['DateTimeZone'])); // *
			} else {
				$d = new \DateTime();
			}

			$at = $d->format('Y-m-d G:i:s');

			$set .= ", {$this->updatedAt} = '$at'";
		}

		if (!empty($this->where)) {
			$where = implode(' AND ', $this->where);
		} else {
			$where = '';
		}

		if (!empty($this->where_raw_q)) {
			if (!empty($where)) {
				$where = $this->where_raw_q . " AND $where";
			} else {
				$where = $this->where_raw_q;
			}
		}

		if (trim($where) == '') {
			throw new SqlException("WHERE can not be empty in UPDATE statement");
		}

		$q = "UPDATE " . DB::quote($this->from()) .
			" SET $set WHERE " . $where;

		// dd($q, 'Update statement');

		/*
			JSON no puede ser un string vacio ('')
		*/
		foreach ($vals as $ix => $val) {
			if (isset($this->schema['attr_type_detail'][$vars[$ix]]) && in_array($this->schema['attr_type_detail'][$vars[$ix]], ['JSON'])) {
				if ($vals[$ix] == '') {
					$vals[$ix] = null;
				}
			}
		}

		$vals = array_merge($vals, $this->w_vals);
		$vars = array_merge($vars, $this->w_vars);

		///////////////[ BUG FIXES ]/////////////////

		// $_vals = [];
		// $reps  = 0;
		// foreach($vals as $ix => $val)
		// {				
		// 	if($val === NULL){
		// 		$q = Strings::replaceNth('?', 'NULL', $q, $ix+1-$reps);
		// 		$reps++;

		// 	/*
		// 		Corrección para operaciones entre enteros y floats en PGSQL
		// 	*/
		// 	} elseif(DB::driver() == DB::PGSQL && is_float($val)){ 
		// 		$q = Strings::replaceNth('?', 'CAST(? AS DOUBLE PRECISION)', $q, $ix+1-$reps);
		// 		$reps++;
		// 		$_vals[] = $val;
		// 	} else {
		// 		$_vals[] = $val;
		// 	}
		// }

		// $vals = $_vals;

		///////////////////////////////////////////

		if ($this->semicolon_ending) {
			$q .= ';';
		}

		// dd($vals, 'vals');
		// dd($q, 'q');

		$st = $this->conn->prepare($q);

		foreach ($vals as $ix => $val) {
			if (is_array($val)) {
				if (isset($this->schema['attr_types'][$vars[$ix]]) && !$this->schema['attr_types'][$vars[$ix]] == 'STR') {
					throw new \InvalidArgumentException("Param '{[$vars[$ix]}' is not expected to be an string. Given array");
				} else {
					$val = json_encode($val);
					$type = \PDO::PARAM_STR;
				}
			} else {
				if (is_null($val)) {
					$type = \PDO::PARAM_NULL;
				} elseif (isset($vars[$ix]) && isset($this->schema['attr_types'][$vars[$ix]])) {
					$const = $this->schema['attr_types'][$vars[$ix]];
					$type = constant("PDO::PARAM_{$const}");
				} elseif (is_int($val))
					$type = \PDO::PARAM_INT;
				elseif (is_bool($val))
					$type = \PDO::PARAM_BOOL;
				elseif (is_string($val))
					$type = \PDO::PARAM_STR;
			}

			$st->bindValue($ix + 1, $val, $type);
		}

		$this->last_bindings = $vals;
		$this->last_pre_compiled_query = $q;
		$this->last_operation = ($this->current_operation !== null) ? $this->current_operation : 'update';

		// var_dump($vals);
		// dd($this->last_bindings, $this->last_pre_compiled_query);

		if (!$this->exec) {
			return 0;
		}

		if ($st->execute()) {
			$count = $st->rowCount();

			switch ($this->current_operation) {
				case 'restore':
					$this->onRestored($data, $count);
					break;
				case 'delete':
					$this->onDeleted($data, $count);
					break;
				default:
					$this->onUpdated($data, $count);
			}
		} else
			$count = false;

		return $count;
	}

	function updateOrFail(array $data, $set_updated_at = true)
	{
		if (!$this->exists()) {
			throw new \Exception("Resource does not exist");
		}

		if (!$this->exec) {
			return 0;
		}

		return $this->update($data, $set_updated_at);
	}

	function touch()
	{
		$this->fill([$this->updatedAt()]);
		return $this->update([$this->updatedAt() => at()]);
	}

	function setSoftDelete(bool $status)
	{
		if (!$this->inSchema([$this->deletedAt])) {
			if ($status) {
				throw new SqlException("There is no $this->deletedAt for table '" . $this->from() . "' in the attr_types");
			}
		}

		$this->soft_delete = $status;
		return $this;
	}

	/**
	 * delete
	 *
	 * @param  array $data (aditional fields in case of soft-delete)
	 * @return mixed
	 */
	function delete(bool $soft_delete = true, array $data = [])
	{
		if ($this->conn == null)
			throw new SqlException('No conection');

		// Validación
		if (!empty($this->validator)) {
			$validado = $this->validator->validate(array_combine($this->w_vars, $this->w_vals), $this->getRules());

			if ($validado !== true) {
				throw new InvalidValidationException(json_encode(
					$this->validator->getErrors()
				));
			}
		}

		if ($this->soft_delete && $soft_delete) {
			$at = at();

			$to_fill = [];
			if (!empty($data)) {
				$to_fill = array_keys($data);
			}
			$to_fill[] = $this->deletedAt;

			$data =  array_merge($data, [$this->deletedAt => $at]);

			$this->fill($to_fill);

			$this->current_operation = 'delete';
			$ret = $this->update($data, false);
			$this->last_operation    = 'delete';

			return $ret;
		}

		$this->onDeleting($data);

		$where = '';
		if (!empty($this->where)) {
			$where = implode(' AND ', $this->where);
		}

		$where = trim($where);

		if (empty($where) && empty($this->where_raw_q)) {
			throw new \Exception("DELETE statement requieres WHERE condition");
		}

		if (!empty($this->where_raw_q)) {
			if (!empty($where)) {
				$where = $this->where_raw_q . " AND $where";
			} else {
				$where = $this->where_raw_q;
			}
		}

		$q = "DELETE FROM " . DB::quote($this->from()) . " WHERE " . $where;

		if ($this->semicolon_ending) {
			$q .= ';';
		}

		if ($this->bind) {
			$st = $this->bind($q);
			$this->last_bindings = $this->getBindings();
		}

		$this->last_pre_compiled_query = $q;
		$this->last_operation = 'delete';

		if ($this->exec && $st->execute()) {
			$count = $st->rowCount();
			$this->onDeleted($data, $count);
		} else
			$count = false;

		return $count;
	}

	function forceDelete()
	{
		$this->delete(false);
	}

	protected function checkUndeletePreconditions(): bool
	{
		if (!$this->soft_delete) {
			throw new \Exception("Undelete is not available");
		}

		$where = '';
		if (!empty($this->where)) {
			$where = implode(' AND ', $this->where);
		}

		$where = trim($where);

		if (empty($where) && empty($this->where_raw_q)) {
			throw new \Exception("Lacks WHERE condition");
		}

		return true;
	}

	// debe remover cualquier condición que involucre a $this->deletedAt en el WHERE !!!!
	function deleted($state = true)
	{
		$this->show_deleted = $state;
		return $this;
	}

	// alias de deleted()
	function withTrashed()
	{
		return $this->deleted(true);
	}

	function onlyTrashed()
	{
		$this->deleted();
		$this->whereNotNull($this->deletedAt());
		return $this;
	}

	/*
		Devuelve si la row fue borrada
	*/
	function trashed(): bool
	{
		$this->checkUndeletePreconditions();
		$this->onlyTrashed();
		return $this->exists();
	}

	// alias
	function is_trashed(): bool
	{
		return $this->trashed();
	}


	/*
		Si el undelete está disponible intenta restaurar sin chequear si la row fue previamente borrada
	*/
	function undelete()
	{
		$this->current_operation = 'restore';
		$this->checkUndeletePreconditions();

		if (isset($this->config)) {
			$d = new \DateTime('', new \DateTimeZone($this->config['DateTimeZone']));
		} else {
			$d = new \DateTime();
		}

		$at = at();

		$this->fill([$this->deletedAt]);

		$ret = $this->update([
			$this->deletedAt() => NULL
		], false);

		$this->current_operation = null;
		return $ret;
	}

	// alias for delete()
	function restore()
	{
		return $this->undelete();
	}

	function truncate()
	{
		DB::truncate($this->table_name);
		return $this;
	}

	/*
		Si un campo es enviado al Modelo pero realmente no existe en el schema
		entonces se debe ignorar para evitar generar un error innecesario.
	*/
	protected function ignoreFieldsNotPresentInSchema(array &$data)
	{
		if (empty($this->schema)) {
			return;
		}

		foreach ($data as $key => $dato) {
			if (!in_array($key, $this->getFillables())) {
				unset($data[$key]);
			}
		}
	}

	private function getParamType($val): int
	{
		if (is_null($val)) {
			return \PDO::PARAM_NULL; // 0
		} elseif (is_int($val)) {
			return \PDO::PARAM_INT;  // 1
		} elseif (is_bool($val)) {
			return \PDO::PARAM_BOOL; // 5
		} elseif (is_string($val)) {
			if (mb_strlen($val) < 4000) {
				return \PDO::PARAM_STR;  // 2
			} else {
				return \PDO::PARAM_LOB;  // 3
			}
		} elseif (is_float($val)) {
			return \PDO::PARAM_STR;  // 2
		} elseif (is_resource($val)) {
			// https://stackoverflow.com/a/36724762/980631
			return \PDO::PARAM_LOB;  // 3
		} elseif (is_array($val)) {
			throw new \Exception("where value can not be an array!");
		} else {
			throw new \Exception("Unsupported type: " . var_export($val, true));
		}
	}

	/*
		@return mixed false | integer 

		Si la data es un array de arrays, intenta un INSERT MULTIPLE
	*/
	function create(array $data, $ignore_duplicates = false)
	{
		$this->current_operation = 'create';

		// dd($data, __FUNCTION__ . "( `{$this->table_name}` )");

		if ($this->conn == null && $this->exec)
			throw new SqlException('No connection');

		if (!Arrays::isAssoc($data)) {
			foreach ($data as $dato) {
				if (is_array($dato)) {
					$last_id = $this->create($dato, $ignore_duplicates);
				} else {
					throw new \InvalidArgumentException('Array of data should be associative');
				}
			}
		}

		// control de recursión para INSERT múltiple
		if (isset($data[0]) && is_array($data[0])) {
			return $last_id ?? null;
		}

		$this->ignoreFieldsNotPresentInSchema($data);

		$this->data = $data;

		$data = $this->applyInputMutator($data, 'CREATE');
		$vars = array_keys($data);
		$vals = array_values($data);

		// Event hook
		$this->onCreating($data);

		if ($this->inSchema([$this->createdAt]) && !isset($data[$this->createdAt])) {
			$this->fill([$this->createdAt]);

			$at = datetime();
			$data[$this->createdAt] = $at;

			$vars = array_keys($data);
			$vals = array_values($data);
		}

		// dd($this->fillable, 'FILLABLE');
		// dd($this->not_fillable, 'NOT FILLABLE');

		// Validación
		if (!empty($this->validator)) {
			$validado = $this->validator->validate($data, $this->getRules(), $this->fillable, $this->not_fillable);
			if ($validado !== true) {
				// dd($this->validator->getErrors());

				throw new InvalidValidationException(json_encode(
					$this->validator->getErrors()
				));
			}
		}

		// Convert array values (like 'files') to JSON before mapping
		foreach ($vals as $ix => $val) {
			if (is_array($val)) {
				$vals[$ix] = json_encode($val);
			}
		}

		$symbols  = array_map(function (?string $e = null) {
			if ($e === null) {
				$e = '';
			}

			return ':' . $e;
		}, $vars);

		$q_marks  = array_map(function (?string $e = null) {
			if ($e === null) {
				$e = '';
			}

			return "'$e'";
		}, $vals);

		/*
			BLOB, TEXT, GEOMETRY or JSON columns can't have a default value
		*/
		foreach ($vals as $ix => $val) {
			if (isset($this->schema['attr_type_detail'][$vars[$ix]]) && in_array($this->schema['attr_type_detail'][$vars[$ix]], ['JSON', 'TEXT', 'BLOB', 'GEOMETRY'])) {
				if ($vals[$ix] == '') {
					$vals[$ix] = null;
				}
			}
		}

		if (DB::driver() == DB::MYSQL || DB::isMariaDB()) {
			$str_vars = implode(', ', array_map(function ($var) {
				return "`$var`";
			}, $vars));
		} else {
			$str_vars = implode(', ', $vars);
		}

		$str_vals     = implode(', ', $symbols);

		$str_qmarks   = implode(', ', $q_marks);

		$this->insert_vars = $vars;

		$q                       = "INSERT INTO " . DB::quote($this->from()) . " ($str_vars) VALUES ($str_vals)";
		$this->last_compiled_sql = "INSERT INTO " . DB::quote($this->from()) . " ($str_vars) VALUES ($str_qmarks)";

		if ($this->semicolon_ending) {
			$q .= ';';
		}

		$st = $this->conn->prepare($q);

		foreach ($vals as $ix => $val) {
			if (is_array($val)) {
				if (isset($this->schema['attr_types'][$vars[$ix]]) && !$this->schema['attr_types'][$vars[$ix]] == 'STR') {
					throw new \InvalidArgumentException("Param '{[$vars[$ix]}' is not expected to be an string. Given array");
				} else {
					$vals[$ix] = json_encode($val);
					$type = \PDO::PARAM_STR;
				}
			} else {
				if (is_null($val)) {
					$type = \PDO::PARAM_NULL;
				} elseif (isset($vars[$ix]) && $this->schema != NULL && isset($this->schema['attr_types'][$vars[$ix]])) {
					$const = $this->schema['attr_types'][$vars[$ix]];
					$type = constant("PDO::PARAM_{$const}");
				} elseif (is_int($val))
					$type = \PDO::PARAM_INT;
				elseif (is_bool($val))
					$type = \PDO::PARAM_BOOL;
				elseif (is_string($val))
					$type = \PDO::PARAM_STR;
			}

			// dd($type, "TYPE");	
			// dd([$vals[$ix], $symbols[$ix], $type]);

			$st->bindParam($symbols[$ix], $vals[$ix], $type);
		}

		$this->last_bindings = $vals;
		$this->last_pre_compiled_query = $q;
		$this->last_operation = 'create';

		if ($this->executionMode == self::EXECUTION_MODE_PREVIEW) {
			// Retornar información sobre la operación sin ejecutar
			return [
				'operation' => 'create',
				'table' => $this->table_name,
				'data' => $data,
				'sql' => $this->_dd($q, $vals),
				'bindings' => $vals
			];
		} else if ($this->executionMode == self::EXECUTION_MODE_SIMULATE) {
			$this->logSQL();

			// Simular la operación
			$this->last_inserted_id = -1; // ID ficticio
			$this->onCreated($data, $this->last_inserted_id);
			return $this->last_inserted_id;
		}

		if (!$this->exec) {
			$this->logSQL();

			// Ejecuto igual el hook a fines de poder ver la query con dd()
			$this->onCreated($data, null);
			return NULL;
		}

		try {
			$result = $st->execute();
		} catch (\PDOException $e) {
			$this->logSQL();

			$debug = Config::get()['debug'];

			// Verificar si es un error de duplicado
			$is_duplicate_error = Strings::contains('1062 Duplicate entry', $e->getMessage());

			// Si ignore_duplicates está activado Y es un error de duplicado, simplemente retornar null
			if ($ignore_duplicates && $is_duplicate_error) {
				return null;
			}

			// Para cualquier otro error, lanzar excepción
			if ($debug) {
				$msg = "Error inserting data from " . $this->from() . ' - ' . $e->getMessage();
			} else {
				$msg = 'Error inserting data';
			}

			throw new \PDOException($msg);
		}

		$this->current_operation = null;

		if (!isset($result)) {
			return;
		}

		if ($result) {
			// sin schema no hay forma de saber la PRI Key. Intento con 'id' 
			if ($this->schema != null && $this->schema['id_name'] != null) {
				$id_name = $this->schema['id_name'];
			} else {
				$id_name = 'id';
			}

			if (isset($data[$id_name])) {
				$this->last_inserted_id =	$data[$id_name];
			} else {
				$this->last_inserted_id = $this->conn->lastInsertId();
			}

			$this->onCreated($data, $this->last_inserted_id);
		} else {
			$this->last_inserted_id = false;
		}

		return $this->last_inserted_id;
	}

	function createOrIgnore(array $data)
	{
		$this->create($data, true);
	}

	/**
	 * Create or update a record based on unique fields
	 * 
	 * @param array $data Data to create/update
	 * @param array|null $uniqueFields Fields to check for existing record. If null, uses schema unique fields
	 * @return mixed Last inserted ID or number of updated records
	 */
	function createOrUpdate(array $data, ?array $uniqueFields = null)
	{
		// Determinar los campos únicos a utilizar
		if ($uniqueFields === null) {
			$uniqueFields = $this->getUniques() ?? [];
		}

		if (empty($uniqueFields)) {
			// Si no hay campos únicos, simplemente insertar
			return $this->create($data);
		}

		// Verificar si el registro ya existe
		$conditions = [];
		foreach ($uniqueFields as $field) {
			if (isset($data[$field])) {
				$conditions[] = [$field, $data[$field]];
			}
		}

		if (empty($conditions)) {
			// Si no pudimos construir condiciones para verificar, simplemente insertar
			return $this->create($data);
		}

		// Consultar existencia
		$exists = clone $this;
		$recordExists = $exists->where($conditions)->exists();

		// En modo simulación, registramos la consulta pero continuamos el flujo
		if ($this->executionMode == self::EXECUTION_MODE_SIMULATE) {
			$this->logSQL();
		}

		if ($recordExists && $this->executionMode != self::EXECUTION_MODE_SIMULATE) {
			// Actualizar registro existente
			return $this->where($conditions)->update($data);
		} else {
			// Insertar nuevo registro
			return $this->create($data);
		}
	}

	/**
	 * Main method for inserting records with full model lifecycle
	 *
	 * @param array $data Single record or array of records
	 * @param bool $useTransaction Whether to wrap in transaction
	 * @param bool $ignore_duplicates If true, ignore duplicate key errors and return null
	 * @return mixed Last inserted ID, null if duplicate ignored, or false on failure
	 * @throws \Exception On validation or database errors
	 */
	function insert(array $data, bool $useTransaction = true, bool $ignore_duplicates = false)
	{
		if ($this->conn == null) {
			throw new SqlException('No connection');
		}

		// dd($data, __FUNCTION__ . "( `{$this->table_name}` )");

		// Single record
		if (Arrays::isAssoc($data)) {
			if ($useTransaction) {
				DB::beginTransaction();
			}

			try {
				$this->ignoreFieldsNotPresentInSchema($data);
				$this->data = $data;

				$this->onCreating($data);
				$data = $this->applyInputMutator($data, 'CREATE');

				if (!empty($this->validator)) {
					$validado = $this->validator->validate($data, $this->getRules());
					if ($validado !== true) {
						throw new InvalidValidationException(json_encode($this->validator->getErrors()));
					}
				}

				if ($this->inSchema([$this->createdAt]) && !isset($data[$this->createdAt])) {
					$this->fill([$this->createdAt]);
					$data[$this->createdAt] = datetime();
				}

				// dd($data); //

				$ret = $this->executeInsert($data, $ignore_duplicates);

				if ($useTransaction) {
					DB::commit();
				}

				$this->onCreated($data, $ret);
				return $ret;
			} catch (\Exception $e) {
				if ($useTransaction) {
					DB::rollback();
				}
				throw $e;
			}
		}

		// Multiple records
		if ($useTransaction) {
			DB::beginTransaction();
		}

		try {
			$ret = null;
			foreach ($data as $record) {
				$ret = $this->insert($record, false, $ignore_duplicates);
			}

			if ($useTransaction) {
				DB::commit();
			}
			return $ret;
		} catch (\Exception $e) {
			if ($useTransaction) {
				DB::rollback();
			}
			throw $e;
		}
	}

	/**
	 * Direct database insert bypassing hooks and mutators
	 * Use with caution - skips model lifecycle events
	 * 
	 * @param array $data Data to insert
	 * @return mixed Last inserted ID or false
	 */
	function rawInsert(array $data)
	{
		if ($this->conn == null && $this->exec) {
			throw new SqlException('No connection');
		}

		if (Arrays::isAssoc($data)) {
			return $this->executeInsert($data);
		}

		$ret = null;
		foreach ($data as $record) {
			$ret = $this->executeInsert($record);
		}
		return $ret;
	}

	/**
	 * Optimized bulk insert using single query
	 * 
	 * @param array $data Array of records to insert
	 * @param int $batchSize Maximum records per query
	 * @return mixed Last inserted ID or false
	 */
	function bulkInsert(array $data, int $batchSize = 1000)
	{
		if ($this->conn == null && $this->exec) {
			throw new SqlException('No connection');
		}

		if (empty($data)) {
			throw new \InvalidArgumentException("No data provided for bulk insert");
		}

		// Get fields from first record
		$first_record = reset($data);
		if (!is_array($first_record) || Arrays::isAssoc($first_record)) {
			throw new \InvalidArgumentException("Data must be array of arrays");
		}

		$fields = array_keys($first_record);
		if ($this->inSchema([$this->createdAt]) && !isset($first_record[$this->createdAt])) {
			$fields[] = $this->createdAt;
			$at = datetime();
		}

		// Event hook para toda la operación
		$this->onCreating($data);

		// Si estamos en modo de simulación temprana, retornamos un ID ficticio
		if ($this->executionMode == self::EXECUTION_MODE_SIMULATE) {
			$this->last_inserted_id = -1; // ID ficticio
			$this->onCreated($data, $this->last_inserted_id);
			return $this->last_inserted_id;
		}

		// Preview para todo el conjunto de datos
		if ($this->executionMode == self::EXECUTION_MODE_PREVIEW) {
			$preview_data = [];
			$batch_count = 0;

			foreach (array_chunk($data, $batchSize) as $batch) {
				$batch_count++;
				// Preparamos los datos como lo haríamos para la inserción real
				$all_values = [];
				$placeholders = [];

				foreach ($batch as $record) {
					$record_values = [];
					foreach ($fields as $field) {
						if ($field === $this->createdAt) {
							$record_values[] = $at;
						} else {
							$value = $record[$field] ?? null;
							if (is_array($value)) {
								$value = json_encode($value);
							}
							$record_values[] = $value;
						}
					}

					$all_values = array_merge($all_values, $record_values);
					$placeholders[] = '(' . implode(',', array_fill(0, count($fields), '?')) . ')';
				}

				// Build query for preview
				$fields_str = implode(',', $fields);
				$values_str = implode(',', $placeholders);

				$q = "INSERT INTO " . DB::quote($this->from()) . " ($fields_str) VALUES $values_str";

				$preview_data["batch_$batch_count"] = [
					'record_count' => count($batch),
					'sql' => $q,
					'bindings_count' => count($all_values),
					'formatted_sample' => $this->_dd($q, array_slice($all_values, 0, min(10, count($all_values)))) // Muestra solo los primeros 10 bindings
				];
			}

			return [
				'operation' => 'bulk_insert',
				'table' => $this->table_name,
				'total_records' => count($data),
				'batch_size' => $batchSize,
				'batch_count' => $batch_count,
				'batches' => $preview_data
			];
		}

		// Process in batches
		$last_id = null;
		foreach (array_chunk($data, $batchSize) as $batch) {
			$all_values = [];
			$placeholders = [];

			foreach ($batch as $record) {
				$record_values = [];
				foreach ($fields as $field) {
					if ($field === $this->createdAt) {
						$record_values[] = $at;
					} else {
						$value = $record[$field] ?? null;
						if (is_array($value)) {
							$value = json_encode($value);
						}
						$record_values[] = $value;
					}
				}

				$all_values = array_merge($all_values, $record_values);
				$placeholders[] = '(' . implode(',', array_fill(0, count($fields), '?')) . ')';
			}

			// Build and execute query
			$fields_str = implode(',', $fields);
			$values_str = implode(',', $placeholders);

			$q = "INSERT INTO " . DB::quote($this->from()) . " ($fields_str) VALUES $values_str";

			$st = $this->conn->prepare($q);

			// Bind values
			foreach ($all_values as $index => $value) {
				$type = $this->getParamType($value);
				$st->bindValue($index + 1, $value, $type);
			}

			$this->last_bindings = $all_values;
			$this->last_pre_compiled_query = $q;
			$this->last_operation = 'bulk_insert';

			// Si estamos en modo SIMULATE, simulamos la operación sin ejecutarla
			if ($this->executionMode == self::EXECUTION_MODE_SIMULATE) {
				$this->logSQL();
				continue; // Seguimos con el siguiente lote sin ejecutar
			}

			if (!$this->exec) {
				$this->logSQL();
				continue;
			}

			if (!$st->execute()) {
				return false;
			}

			$last_id = $this->conn->lastInsertId();
		}

		// Hook después de toda la operación
		if ($this->executionMode == self::EXECUTION_MODE_SIMULATE) {
			$this->last_inserted_id = -1; // ID ficticio
			$this->onCreated($data, $this->last_inserted_id);
			return $this->last_inserted_id;
		}

		return $last_id;
	}

	/**
	 * Helper method to execute single record insert
	 * Used by both insert() and rawInsert()
	 */
	private function executeInsert(array $data, bool $ignore_duplicates = false)
	{
		$vars = array_keys($data);
		$placeholders = array_fill(0, count($vars), '?');

		$fields_str = implode(',', $vars);
		$values_str = implode(',', $placeholders);

		$q = "INSERT INTO " . DB::quote($this->from()) . " ($fields_str) VALUES ($values_str)";

		$st = $this->conn->prepare($q);

		// Preparamos los valores y tipos
		$values = [];
		$types = [];

		$index = 1; // En lugar de usar $index como la clave del array
		foreach ($data as $field => $value) {
			list($processed_val, $type) = $this->getBindValueAndType($value, $field);
			$values[] = $processed_val;
			$types[] = $type;
			$index++;
		}

		$this->last_bindings = array_values($data);
		$this->last_pre_compiled_query = $q;
		$this->last_operation = 'create';

		// Si estamos en modo preview, retornamos información sin ejecutar
		if ($this->executionMode == self::EXECUTION_MODE_PREVIEW) {
			return [
				'operation' => 'insert',
				'table' => $this->table_name,
				'data' => $data,
				'sql' => $q,
				'bindings' => $values,
				'formatted_sql' => $this->_dd($q, $values)
			];
		}

		// Si estamos en modo simulate, registramos la SQL pero no ejecutamos
		if ($this->executionMode == self::EXECUTION_MODE_SIMULATE) {
			$this->logSQL();
			return -1; // ID ficticio
		}

		// Si no debemos ejecutar, solo registramos y salimos
		if (!$this->exec) {
			$this->logSQL();
			return null;
		}

		// Hacemos el binding de valores
		$index = 1;
		foreach ($values as $i => $val) {
			$st->bindValue($index++, $val, $types[$i]);
		}

		try {
			$result = $st->execute();
			if (!$result) {
				return false;
			}
			return $this->conn->lastInsertId();
		} catch (\PDOException $e) {
			$this->logSQL();

			$debug = Config::get()['debug'];

			// Verificar si es un error de duplicado
			$is_duplicate_error = Strings::contains('1062 Duplicate entry', $e->getMessage());

			// Si ignore_duplicates está activado Y es un error de duplicado, simplemente retornar null
			if ($ignore_duplicates && $is_duplicate_error) {
				return null;
			}

			// Para cualquier otro error, lanzar excepción
			if ($debug) {
				$msg = "Error inserting data from " . $this->from() . ' - ' . $e->getMessage();
			} else {
				$msg = 'Error inserting data';
			}

			throw new \PDOException($msg);
		}
	}

	function insertOrIgnore(array $data)
	{
		return $this->insert($data, true, true);
	}

	/**
	 * Insert or update multiple records based on unique fields
	 * Wraps operations in a transaction
	 * 
	 * @param array $data Array of records to create/update
	 * @param array|null $uniqueFields Fields to check for existing records
	 * @return mixed Last inserted ID or false on failure
	 */
	function insertOrUpdate(array $data, ?array $uniqueFields = null)
	{
		// Si estamos en modo preview, podemos retornar información de todas las operaciones
		// que se realizarían, sin necesidad de procesar cada registro
		if ($this->executionMode == self::EXECUTION_MODE_PREVIEW) {
			if (!Arrays::isAssoc($data)) {
				$preview_data = [];
				foreach ($data as $i => $record) {
					$preview_data[] = $this->previewCreateOrUpdate($record, $uniqueFields);
				}

				return [
					'operation' => 'insert_or_update_batch',
					'table' => $this->table_name,
					'record_count' => count($data),
					'records' => $preview_data
				];
			} else {
				return $this->previewCreateOrUpdate($data, $uniqueFields);
			}
		}

		// Procesamiento normal de operaciones múltiples
		if (!Arrays::isAssoc($data)) {
			if (is_array($data[0])) {
				// En modo simulate, no necesitamos una transacción real
				if ($this->executionMode == self::EXECUTION_MODE_SIMULATE) {
					$ret = null;
					foreach ($data as $record) {
						$ret = $this->createOrUpdate($record, $uniqueFields);
					}
					return $ret;
				}

				// En modo normal, utilizamos una transacción
				DB::beginTransaction();

				try {
					$ret = null;
					foreach ($data as $record) {
						$ret = $this->createOrUpdate($record, $uniqueFields);
					}
					DB::commit();
					return $ret;
				} catch (\Exception $e) {
					DB::rollback();

					if (Config::get()['debug']) {
						$msg = "Error inserting/updating data in " . $this->from() .
							' - ' . $e->getMessage() .
							' - SQL: ' . $this->getLog();
					} else {
						$msg = 'Error inserting/updating data';
					}

					throw new \Exception($msg);
				}
			} else {
				throw new \InvalidArgumentException('Array of data should be associative');
			}
		} else {
			// En modo simulate, no necesitamos una transacción real
			if ($this->executionMode == self::EXECUTION_MODE_SIMULATE) {
				return $this->createOrUpdate($data, $uniqueFields);
			}

			// En modo normal, utilizamos una transacción
			DB::beginTransaction();

			try {
				$ret = $this->createOrUpdate($data, $uniqueFields);
				DB::commit();
				return $ret;
			} catch (\Exception $e) {
				DB::rollback();

				if (Config::get()['debug']) {
					$msg = "Error inserting/updating data in " . $this->from() .
						' - ' . $e->getMessage() .
						' - SQL: ' . $this->getLog();
				} else {
					$msg = 'Error inserting/updating data';
				}

				throw new \Exception($msg);
			}
		}
	}

	/**
	 * Genera una vista previa de la operación createOrUpdate sin ejecutarla
	 *
	 * @param array $data Datos a insertar/actualizar
	 * @param array|null $uniqueFields Campos únicos para verificar
	 * @return array Información sobre la operación
	 */
	private function previewCreateOrUpdate(array $data, ?array $uniqueFields = null)
	{
		// Determinar los campos únicos a utilizar
		if ($uniqueFields === null) {
			$uniqueFields = $this->getUniques() ?? [];
		}

		if (empty($uniqueFields)) {
			// Si no hay campos únicos, sería solo una inserción
			return [
				'operation' => 'insert',
				'table' => $this->table_name,
				'data' => $data,
				'sql' => $this->previewInsertSQL($data),
				'notes' => 'No unique fields provided - would perform insert only'
			];
		}

		// Construir la consulta para verificar existencia
		$conditions = [];
		foreach ($uniqueFields as $field) {
			if (isset($data[$field])) {
				$conditions[] = [$field, $data[$field]];
			}
		}

		// Si no hay condiciones válidas basadas en los campos proporcionados, sería una inserción
		if (empty($conditions)) {
			return [
				'operation' => 'insert',
				'table' => $this->table_name,
				'data' => $data,
				'sql' => $this->previewInsertSQL($data),
				'notes' => 'No matching conditions for unique fields - would perform insert only'
			];
		}

		// Previsualizar la consulta de búsqueda
		$findQuery = clone $this;
		$findQuery->where($conditions);
		$findSQL = $findQuery->toSql();

		return [
			'operation' => 'insert_or_update',
			'table' => $this->table_name,
			'data' => $data,
			'check_operation' => [
				'sql' => $findSQL,
				'bindings' => $findQuery->getBindings(),
				'formatted_sql' => $findQuery->dd()
			],
			'if_exists' => [
				'operation' => 'update',
				'sql' => $this->previewUpdateSQL($data, $conditions),
			],
			'if_not_exists' => [
				'operation' => 'insert',
				'sql' => $this->previewInsertSQL($data),
			],
			'notes' => 'Would check for existence before determining whether to insert or update'
		];
	}

	/**
	 * Genera SQL para inserción (solo para previsualización)
	 */
	private function previewInsertSQL(array $data)
	{
		$fields = array_keys($data);
		$placeholders = array_fill(0, count($fields), '?');

		$fields_str = implode(', ', $fields);
		$placeholders_str = implode(', ', $placeholders);

		$sql = "INSERT INTO " . DB::quote($this->from()) . " ($fields_str) VALUES ($placeholders_str)";

		return $this->_dd($sql, array_values($data));
	}

	/**
	 * Genera SQL para actualización (solo para previsualización)
	 */
	private function previewUpdateSQL(array $data, array $conditions)
	{
		$updateFields = [];
		foreach ($data as $field => $value) {
			$updateFields[] = "$field = ?";
		}

		$updateStr = implode(', ', $updateFields);

		$whereStr = [];
		foreach ($conditions as $condition) {
			$whereStr[] = "{$condition[0]} = ?";
		}

		$whereClause = implode(' AND ', $whereStr);

		$sql = "UPDATE " . DB::quote($this->from()) . " SET $updateStr WHERE $whereClause";

		$bindings = array_values($data);
		foreach ($conditions as $condition) {
			$bindings[] = $condition[1];
		}

		return $this->_dd($sql, $bindings);
	}


	function getInsertVals()
	{
		return $this->insert_vars;
	}

	/**
	 * Obtiene el valor procesado y el tipo de parámetro PDO para vinculación, manejando tipos del esquema y arreglos JSON 
	 *
	 * (Corregida el 28/02/2025 por posible error de operador lógico en if)
	 * 
	 * @param mixed $val El valor a verificar
	 * @param string|null $field El nombre del campo del esquema (opcional)
	 * @return array Devuelve [valor_procesado, tipo_parametro]
	 * @throws \InvalidArgumentException Si el tipo del valor no es válido para el campo especificado
	 * @throws \Exception Si se encuentra un tipo no soportado
	 */
	private function getBindValueAndType($val, ?string $field = null): array
	{
		// Manejo de arreglos (potencialmente JSON)
		if (is_array($val)) {
			if (
				$field !== null &&
				isset($this->schema['attr_types'][$field]) &&
				$this->schema['attr_types'][$field] !== 'STR'
			) {
				throw new \InvalidArgumentException(
					"El campo '{$field}' debe ser de tipo 'STR' para aceptar arreglos, pero se especificó '{$this->schema['attr_types'][$field]}'."
				);
			}
			return [json_encode($val), \PDO::PARAM_STR];
		}

		// Manejo de tipos definidos en el esquema
		if ($field !== null && isset($this->schema['attr_types'][$field])) {
			$const = $this->schema['attr_types'][$field];
			$paramType = constant("PDO::PARAM_{$const}");
			if ($paramType === null) {
				throw new \Exception(
					"El tipo '{$const}' especificado en el esquema para el campo '{$field}' no es válido en PDO."
				);
			}
			return [$val, $paramType];
		}

		// Manejo de tipos estándar
		if (is_null($val)) {
			return [$val, \PDO::PARAM_NULL];
		} elseif (is_int($val)) {
			return [$val, \PDO::PARAM_INT];
		} elseif (is_bool($val)) {
			return [$val, \PDO::PARAM_BOOL];
		} elseif (is_string($val)) {
			// Límite de 4000 caracteres ajustable según la base de datos
			if (mb_strlen($val) < 4000) {
				return [$val, \PDO::PARAM_STR];
			} else {
				return [$val, \PDO::PARAM_LOB];
			}
		} elseif (is_float($val)) {
			// Formato consistente para evitar problemas de precisión
			$formattedVal = sprintf("%.10f", $val); // Precisión ajustable
			return [$formattedVal, \PDO::PARAM_STR];
		} elseif (is_resource($val)) {
			return [$val, \PDO::PARAM_LOB];
		} else {
			throw new \Exception(
				"Tipo no soportado para el valor: " . var_export($val, true) . ". Tipos soportados: null, int, bool, string, float, resource."
			);
		}
	}

	/*
		 to be called inside onUpdating() event hook

		 el problema es que necesito ejecutar el mismo WHERE que el UPDATE en un GET para seleccionar el mismo registro y tener contra que comparar.	

		 https://stackoverflow.com/questions/45702409/laravel-check-if-updateorcreate-performed-update/49350664#49350664
		 https://stackoverflow.com/questions/48793257/laravel-check-with-observer-if-column-was-changed-on-update/48793801
	*/

	function isDirty($fields = null)
	{
		if ($fields == null) {
			$fields = $this->attributes;
		}

		if (!is_array($fields)) {
			$fields = [$fields];
		}

		// to be updated
		$keys = array_keys($this->data);

		if (!$this->inSchema($fields)) {
			throw new \Exception("A field was not found in table {$this->table_name}");
		}

		$old_vals = $this->first($fields);
		foreach ($fields as $field) {
			if (!in_array($field, $keys)) {
				continue;
			}

			$new_val = $this->data[$field];

			if ($new_val != $old_vals[$field]) {
				return true;
			}
		}

		return false;
	}


	public function wrap(bool $flag = true)
	{
		$this->wrap_fields = $flag;
		return $this;
	}

	protected function getWrapFieldName($field)
	{
		if ($this->wrap_fields) {
			return DB::quote($field);
		}
		return $field;
	}

	protected function isDecimalField(string $field): bool
	{
		if (empty($this->schema) || empty($this->schema['rules'])) {
			return false;
		}

		return isset($this->schema['rules'][$field]) &&
			isset($this->schema['rules'][$field]['type']) &&
			Strings::startsWith('decimal', strtolower($this->schema['rules'][$field]['type']));
	}

	protected function shouldQuoteDecimals(): bool
	{
		if ($this->decimal_as_string !== null) {
			return $this->decimal_as_string;
		}
		return self::DECIMAL_AS_STRING;
	}

	function getFieldNames()
	{
		return $this->field_names;
	}

	function getformatters()
	{
		return $this->formatters;
	}

	function getHidden()
	{
		return $this->hidden;
	}

	function createdAt()
	{
		return $this->createdAt;
	}

	function createdBy()
	{
		return $this->createdBy;
	}

	function updatedAt()
	{
		return $this->updatedAt;
	}

	function updatedBy()
	{
		return $this->updatedBy;
	}

	function deletedAt()
	{
		return $this->deletedAt;
	}

	function deletedBy()
	{
		return $this->deletedBy;
	}

	function isLocked()
	{
		return $this->is_locked;
	}

	// alias

	function locked()
	{
		return $this->is_locked;
	}

	function belongsTo()
	{
		return $this->belongsTo;
	}

	function getAutoFields()
	{
		return [
			$this->createdBy(),
			$this->createdAt(),
			$this->updatedBy(),
			$this->updatedAt(),
			$this->deletedBy(),
			$this->deletedAt(),
			$this->belongsTo(),
			$this->isLocked()
		];
	}

	function setSqlFormatter(callable $fn)
	{
		static::$sql_formatter_callback = $fn;
	}

	function sqlformatterOff()
	{
		$this->sql_formatter_status = false;

		return $this;
	}

	function sqlformatterOn()
	{
		$this->sql_formatter_status = true;

		return $this;
	}

	static function sqlFormatter(string $query, ...$options): string
	{
		if (!empty(static::$sql_formatter_callback) && is_callable(static::$sql_formatter_callback)) {
			$fn = static::$sql_formatter_callback;

			return $fn($query, ...$options);
		}

		return $query;
	}

	function getSchema()
	{
		return $this->schema;
	}

	function hasSchema()
	{
		return !empty($this->schema);
	}

	/**
	 * inSchema
	 *
	 * @param  array $props
	 *
	 * @return bool
	 */
	function inSchema(array $props)
	{
		// debería chequear que la tabla exista

		if (empty($props))
			throw new SchemaException("Attributes not found");

		foreach ($props as $prop)
			if (!in_array($prop, $this->attributes)) {
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
	function getMissing(array $fields)
	{
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

	function getIdName()
	{
		return $this->schema['id_name'] ?? null; // *
	}

	// alias for getIdName()
	function id()
	{
		return $this->getIdName();
	}

	function getNotHidden()
	{
		return array_diff($this->attributes, $this->hidden);
	}

	function isNullable(string $field)
	{
		return in_array($field, $this->schema['nullable']);
	}

	function isFillable(string $field)
	{
		return in_array($field, $this->fillable) && !in_array($field, $this->not_fillable);
	}

	function getFillables()
	{
		return $this->fillable;
	}

	function getNotFillables()
	{
		return $this->not_fillable;
	}

	function setNullables(array $arr)
	{
		$this->schema['nullable'] = $arr;
	}

	function addNullables(array $arr)
	{
		$this->schema['nullable'] = array_merge($this->schema['nullable'], $arr);
	}

	function removeNullables(array $arr)
	{
		$this->schema['nullable'] = array_diff($this->schema['nullable'], $arr);
	}

	function getNullables()
	{
		return $this->schema['nullable'];
	}

	function getNotNullables()
	{
		return array_diff($this->attributes, $this->schema['nullable']);
	}

	function getUniques()
	{
		return $this->schema['uniques'];
	}

	function getRules()
	{
		return $this->schema['rules'] ?? NULL;
	}

	function getRule(string $name)
	{
		return $this->schema['rules'][$name] ?? NULL;
	}

	function getFieldOrder()
	{
		return $this->field_order;
	}

	/*	
		Adds prefix to raw statements / queries

		it's a partial implementation
	*/
	static function addPrefix(string $st, $tb_prefix = null)
	{
		$tb_prefix = $tb_prefix ?? DB::getTablePrefix() ?? null;

		if (empty($tb_prefix)) {
			return $st;
		}

		// Para evitar agregarlo dos veces
		if (Strings::contains($tb_prefix, $st)) {
			return $st;
		}

		$tb        = Strings::match($st, "/REFERENCES[ ]+`?([^\b^`^ ]+)`?/i");
		$tb_quoted = preg_quote($tb, '/');

		if (!empty($tb)) {
			$st = preg_replace("/REFERENCES[ ]+`$tb_quoted`/i", "REFERENCES `$tb_prefix{$tb}`", $st);
			$st = preg_replace("/REFERENCES[ ]+$tb_quoted/i", "REFERENCES $tb_prefix{$tb}", $st);
		}

		$tb = Strings::match($st, "/CREATE[ ]+TABLE(?: IF NOT EXISTS)?[ ]+`?([^\b^`^ ]+)`?/i");

		if (!empty($tb)) {
			$st = preg_replace("/CREATE TABLE IF NOT EXISTS?[ ]+`$tb`/i", "CREATE TABLE IF NOT EXISTS `$tb_prefix{$tb}`", $st);
			$st = preg_replace("/CREATE TABLE[ ]+`$tb`/i", "CREATE TABLE `$tb_prefix{$tb}`", $st);
			$st = preg_replace("/CREATE TABLE IF NOT EXISTS?[ ]+$tb/i", "CREATE TABLE IF NOT EXISTS $tb_prefix{$tb}", $st);
			$st = preg_replace("/CREATE TABLE[ ]+$tb/i", "CREATE TABLE $tb_prefix{$tb}", $st);

			return $st;
		}

		$tb = Strings::match($st, "/UPDATE[ ]+`?([^\b^`^ ]+)`?/i");

		if (!empty($tb)) {
			$st = preg_replace("/UPDATE[ ]+`$tb`/i", "UPDATE `$tb_prefix{$tb}`", $st);
			$st = preg_replace("/UPDATE[ ]+$tb/i", "UPDATE $tb_prefix{$tb}", $st);

			return $st;
		}

		$tb = Strings::match($st, "/DELETE[ ]+FROM[ ]+`?([^\b^`^ ]+)`?/i");

		if (!empty($tb)) {
			$st = preg_replace("/DELETE[ ]+FROM[ ]+`$tb`/i", "DELETE FROM `$tb_prefix{$tb}`", $st);
			$st = preg_replace("/DELETE[ ]+FROM[ ]+$tb/i", "DELETE FROM $tb_prefix{$tb}", $st);

			return $st;
		}

		$tb = Strings::match($st, "/ALTER[ ]+TABLE[ ]+`?([^\b^`^ ]+)`?/i");

		if (!empty($tb)) {
			$st = preg_replace("/ALTER[ ]+TABLE[ ]+`$tb`/i", "ALTER TABLE `$tb_prefix{$tb}`", $st);
			$st = preg_replace("/ALTER[ ]+TABLE[ ]+$tb/i", "ALTER TABLE $tb_prefix{$tb}", $st);

			return $st;
		}

		$tb = Strings::match($st, "/INSERT[ ]+INTO[ ]+`?([^\b^`^ ]+)`?/i");

		if (!empty($tb)) {
			$st = preg_replace("/INSERT[ ]+INTO[ ]+`$tb`/i", "INSERT INTO `$tb_prefix{$tb}`", $st);
			$st = preg_replace("/INSERT[ ]+INTO[ ]+$tb/i", "INSERT INTO $tb_prefix{$tb}", $st);

			return $st;
		}

		if (Strings::match($st, "/(SELECT)[ ]/i")) {
			$tb = Strings::match($st, "/FROM[ ]+`?([^\b^`^ ]+)`?/i");

			if (!Strings::startsWith('information_schema.tables', $tb) && !empty($tb)) {
				$st = preg_replace("/FROM[ ]+`$tb`/i", "FROM `$tb_prefix{$tb}`", $st);
				$st = preg_replace("/FROM[ ]+$tb/i", "FROM $tb_prefix{$tb}", $st);
			}
		}

		/*
			JOIN es complejo porque el ON puede incluir nombres de tablas 
			o de alias y en este ultimo caso no tendria sentido agregar prefijo

			SELECT Orders.OrderID, Customers.CustomerName
			FROM Orders
			INNER JOIN Customers ON Orders.CustomerID = Customers.CustomerID;
		*/
		$tb = Strings::match($st, "/JOIN[ ]+`?([^\b^`^ ]+)`?/i");

		if (!empty($tb)) {
			$st = preg_replace("/JOIN[ ]+`$tb`/i", "JOIN `$tb_prefix{$tb}`", $st);
			$st = preg_replace("/JOIN[ ]+$tb/i", "JOIN $tb_prefix{$tb}", $st);

			// Aca podria ver de agregar prefijo en la parte del "ON" en JOINs
		}

		return $st;
	}

	/**
	 * Configura el modo de ejecución para operaciones de escritura
	 * 
	 * @param int $mode Uno de los modos EXECUTION_MODE_*
	 * @return $this
	 */
	function setExecutionMode(int $mode)
	{
		if (!in_array($mode, [self::EXECUTION_MODE_NORMAL, self::EXECUTION_MODE_SIMULATE, self::EXECUTION_MODE_PREVIEW])) {
			throw new \InvalidArgumentException("Invalid execution mode: $mode");
		}
		$this->executionMode = $mode;
		return $this;
	}

	/**
	 * Obtiene el modo de ejecución actual
	 * 
	 * @return int
	 */
	function getExecutionMode()
	{
		return $this->executionMode;
	}

	/**
	 * Establece el modo para simular operaciones sin modificar la base de datos
	 * 
	 * @return $this
	 */
	function simulate()
	{
		return $this->setExecutionMode(self::EXECUTION_MODE_SIMULATE);
	}

	/**
	 * Establece el modo para obtener la vista previa de la SQL sin ejecutarla
	 * 
	 * @return $this
	 */
	function preview()
	{
		return $this->setExecutionMode(self::EXECUTION_MODE_PREVIEW);
	}

	/**
	 * Establece el modo normal (ejecutar operaciones realmente)
	 * 
	 * @return $this
	 */
	function normalExecution()
	{
		return $this->setExecutionMode(self::EXECUTION_MODE_NORMAL);
	}

	/////////////////////////////////////////////////////////////
	//
	// Métodos de inserción con SubRecursos
	//
	/////////////////////////////////////////////////////////////
	
	// ...
}

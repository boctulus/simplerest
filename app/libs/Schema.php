<?php

namespace simplerest\libs;

use simplerest\core\Model;
use simplerest\libs\DB;
use simplerest\libs\Strings;
use simplerest\libs\Debug;
use simplerest\libs\Factory;

/*
	Migrations

	The following can be useful :P
	https://hoelz.ro/ref/mysql-alter-table-alter-change-modify-column
	https://mariadb.com/kb/en/auto_increment/
*/

class Schema 
{
	protected $tables;
	protected $tb_name;

	protected $engine = 'InnoDB';
	protected $charset = 'utf8';
	protected $collation;
	
	protected $raw_lines = [];
	protected $fields  = [];
	protected $current_field;
	protected $indices = []; // 'PRIMARY', 'UNIQUE', 'INDEX', 'FULLTEXT', 'SPATIAL'
	protected $fks = [];
	
	protected $prev_schema;
	protected $commands = [];
	protected $query;

	// mysql version
	protected $engine_ver;


	function __construct($tb_name){
		$this->tables = self::getTables();
		$this->engine_ver = (int) DB::select('SELECT VERSION() AS ver')[0]['ver'];
		$this->tb_name = $tb_name;
		$this->fromDB();
	}

	// Válido para MySQL, en un solo sentido
	static function getRelations(string $table = null, string $db = null)
	{
		if ($db == null){
			DB::getConnection();
        	$db  = DB::database();
		}		

        $sql = "SELECT * FROM `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE` 
        WHERE `REFERENCED_TABLE_NAME` IS NOT NULL AND TABLE_SCHEMA = '$db' AND REFERENCED_TABLE_SCHEMA = '$db' ";

		if (!empty($table)){
			$sql .= "AND TABLE_NAME = '$table' ";
		}

		$sql .= "ORDER BY `REFERENCED_COLUMN_NAME`;";

        $rels = Model::query($sql);
        
        $relationships = [];
        foreach($rels as $rel){
            $to_tb = $rel['REFERENCED_TABLE_NAME'];

            $from = $rel['TABLE_NAME'] . '.' . $rel['COLUMN_NAME']; 
            $to   = $rel['REFERENCED_TABLE_NAME'] . '.' . $rel['REFERENCED_COLUMN_NAME']; 

            // "['$to', '$from']"
            $relationships[$to_tb][] = [
                'to'   => $to, 
                'from' => $from
            ];
        }

        foreach ($relationships as $tb => $rs){
            $tos = array_column($rs, 'to');
            //$tos = sort($tos);

            $prev = null;
            $repeted = [];
            foreach ($tos as $to){
                if ($to == $prev){
                    if (!isset($repeted[$tb]) || !in_array($to, $repeted[$tb])){
                        $repeted[$tb][] = $to;
                    }
                }

                $prev = $to;
            }
        }

        foreach ($relationships as $tb => $rs){
            foreach ($rs as $k => $r){
                if (isset($repeted[$tb]) && in_array($r['to'], $repeted[$tb])){
                    list($tb0, $fk0) = explode('.', $r['from']);
                    
                    if (Strings::endsWith('_id', $fk0)){
                        $key = substr($fk0, 0, strlen($fk0) -3);                        
                    }

                    if (!isset($key) && Strings::startsWith('id_', $fk0)){
                        $key = substr($fk0, 3);  
                    } 

					/*
                    if (!isset($key)){
                        $msg = "Invalid convention for FK in table \"$table\" for \"$fk0\". Please name as xxxxx_id\r\n";
						Files::logger($msg, 'errores.txt');
						
						throw new \Exception($msg);
                    }    
					*/

					if (!isset($key)){
						$key = $fk0;
					}
                    
                    $key = $key . 's';  // pluralizo
                    
                    list($tb1, $fk1) = explode('.', $r['to']);
                    $to = "$key.$fk1";
                    
                    unset($relationships[$tb][$k]);

                    $relationships[$tb][] = [
                        'to'   => $to, 
                        'from' => $r['from'] 
                    ];
                }
            }      
        }
        
		return $relationships;
	}

	/*
		Obtiene relaciones con otras tablas de forma bi-direccional
		(desde y hacia esa tabla)
	*/
	static function getAllRelations(string $table, bool $compact = false){
        $relations = [];

        $relations = Schema::getRelations($table);

		if ($relations === null){
			return;
		}

        foreach ($relations as $tb => $rels){
            $arr = [];
            foreach ($rels as $rel){
				if ($compact){
					$cell = "['{$rel['to']}','{$rel['from']}']"; 
				} else {
					$cell = [$rel['to'],$rel['from']]; 
				}       
				
				$arr[] = $cell;
            }

            $relations[$tb] = $arr;
        }

        $more_rels = Schema::getRelations();

        foreach ($more_rels as $tb => $rels){

            foreach ($rels as $rel){
                list($tb1, $fk1) = explode('.', $rel['to']);

                if ($tb1 == $table){
                    list($tb0, $fk0) = explode('.', $rel['from']);
                    
					if ($compact){
						$cell = "['{$rel['from']}','{$rel['to']}']"; 
					} else {
						$cell = [$rel['from'],$rel['to']]; 
					}

                    $relations[$tb0][] = $cell; 
                }
            }
            
        }

        return $relations;
    }

	static function getTables(string $conn_id = null) {	
		$config = Factory::config();
		
		if ($conn_id != null){
			if (!isset($config['db_connections'][$conn_id])){
				throw new \Exception("Connection Id '$conn_id' not defined");
			}			
		} else {
			$conn_id = $config['db_connection_default'];
		}

		$db_name = DB::getCurrentDB();

		return DB::select("SELECT TABLE_NAME 
		FROM information_schema.tables
		WHERE table_schema = '$db_name';", 
		'COLUMN');
	}

	static function FKcheck(bool $status){
		$conn = DB::getConnection();   

		$st = $conn->prepare("SET FOREIGN_KEY_CHECKS=" . ((int) $status) .";");
		$res = $st->execute();
	}

	static function enableForeignKeyConstraints(){
		return self::FKcheck(1);
	}

	static function disableForeignKeyConstraints(){
		return self::FKcheck(0);
	}

	static function hasTable(string $tb_name, string $db_name = null)
	{
		if ($db_name == null){
			$res = DB::select("SHOW TABLES LIKE '$tb_name';");
		}else {
			$res = DB::select("SELECT * 
			FROM information_schema.tables
			WHERE table_schema = '$db_name' 
				AND table_name = '$tb_name'
			LIMIT 1;");
		}

		return (!empty($res));	
	} 

	static function hasColumn(string $table, string $column){
		$conn = DB::getConnection();   

		$res = DB::select("SHOW COLUMNS FROM `$table` LIKE '$column'");
		return !empty($res);
	} 

	static function rename(string $ori, string $final){
		$conn = DB::getConnection();   

		$st = $conn->prepare("RENAME TABLE `$ori` TO `$final`;");
		return $st->execute();
	}	

	static function drop(string $table){
		$conn = DB::getConnection();   

		$st = $conn->prepare("DROP TABLE `{$table}`;");
		return $st->execute();
	}

	static function dropIfExists(string $table){
		$conn = DB::getConnection();   

		$st = $conn->prepare("DROP TABLE IF EXISTS `{$table}`;");
		return $st->execute();
	}


	function tableExists(){
		return in_array($this->tb_name, $this->tables);
	} 

	function columnExists(string $column){
		return static::hasColumn($this->tb_name, $column);
	}

	function setEngine(string $val){
		$this->engine = $val;
		return $this;
	}

	function setCharset(string $val){
		$this->chartset = $val;
		return $this;
	}

	function setCollation(string $val){
		$this->collation = $val;
		return $this;
	}

	function column(string $name){
		$this->current_field = $name;
		return $this;
	}

	function field(string $name){
		return $this->column($name);
	}
	
	// type
	
	function int(string $name, int $len = NULL){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'INT';
		
		if ($len != NULL)
			$this->fields[$this->current_field]['len'] = $len;
		
		return $this;		
	}	
	
	function integer(string $name, int $len = NULL){
		$this->int($name, $len);
		return $this;		
	}	
	
	function serial(string $name, int $len = NULL){		
		$this->current_field = $name;
		//$this->bigint($name, $len)->unsigned()->auto()->unique();
		$this->fields[$this->current_field]['type'] = 'SERIAL';
		return $this;		
	}	
	
	function bigint(string $name){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'BIGINT';
		return $this;		
	}	
	
	function mediumint(string $name){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'MEDIUMINT';
		return $this;		
	}	
	
	function smallint(string $name){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'SMALLINT';
		return $this;		
	}	
	
	function tinyint(string $name){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'TINYINT';
		return $this;		
	}	
	
	function boolean(string $name){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'BOOLEAN';
		return $this;		
	}	
	
	function bool(string $name){
		$this->boolean($name);
		return $this;		
	}
	
	function bit(string $name, int $len){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'BIT';
		$this->fields[$this->current_field]['len'] = $len;		
		return $this;		
	}
	
	function decimal(string $name, int $len = 15, int $len_dec = 4){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'DECIMAL';
		$this->fields[$this->current_field]['len'] = [$len, $len_dec];		
		return $this;		
	}	
	
	function float(string $name){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'FLOAT';
		return $this;		
	}	
	
	function double(string $name){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'DOUBLE';
		return $this;		
	}	
	
	function real(string $name){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'REAL';
		return $this;		
	}	
	
	function char(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'CHAR';
		return $this;		
	}	
	
	function varchar(string $name, int $len = 60){
		if ($len > 65535)
			throw new \InvalidArgumentException("Max length is 65535");
		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'VARCHAR';
		$this->fields[$this->current_field]['len'] = $len;
		return $this;		
	}	
	
	function text(string $name, int $len = NULL){
		if ($len > 65535)
			throw new \InvalidArgumentException("Max length is 65535");
		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'TEXT';
		
		if ($len != NULL)
			$this->fields[$this->current_field]['len'] = $len;
		
		return $this;		
	}	
	
	function tinytext(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'TINYTEXT';
		return $this;		
	}
	
	function mediumtext(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'MEDIUMTEXT';
		return $this;		
	}
	
	function longtext(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'LONGTEXT';
		return $this;		
	}
	
	function varbinary(string $name, int $len = 60){
		if ($len > 65535)
			throw new \InvalidArgumentException("Max length is 65535");
		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'VARBINARY';
		$this->fields[$this->current_field]['len'] = $len;
		return $this;		
	}
	
	function blob(string $name){
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'BLOB';
		return $this;		
	}	
	
	function binary(string $name, int $len){
		if ($len > 255)
			throw new \InvalidArgumentException("Max length is 65535");
		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'BINARY';
		$this->fields[$this->current_field]['len'] = $len;
		return $this;		
	}
	
	function tinyblob(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'TINYBLOB';
		return $this;		
	}
	
	function mediumblob(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'MEDIUMBLOB';
		return $this;		
	}
	
	function longblob(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'LONGBLOB';
		return $this;		
	}
	
	function json(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'JSON';
		return $this;		
	}
	
	function set(string $name, array $values){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'SET';
		$this->fields[$this->current_field]['array'] = $values;
		return $this;		
	}
	
	function enum(string $name, array $values){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'ENUM';
		$this->fields[$this->current_field]['array'] = $values;
		return $this;		
	}
	
	function time(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'TIME';
		return $this;		
	}
	
	function year(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'YEAR';
		return $this;		
	}
	
	function date(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'DATE';
		return $this;		
	}
	
	function datetime(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'DATETIME';
		return $this;		
	}
	
	function timestamp(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'TIMESTAMP';
		return $this;		
	}
	
	function softDeletes(){		
		$this->current_field = 'deleted_at';
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'DATETIME';
		return $this;		
	}
	
	function datetimes(){		
		$this->current_field = 'created_at';
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'DATETIME';
		$this->current_field = 'updated_at';
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'DATETIME';
		return $this;		
	}
	
	function point(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'POINT';
		return $this;		
	}
	
	function multipoint(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'MULTIPOINT';
		return $this;		
	}
	
	function linestring(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'LINESTRING';
		return $this;		
	}
	
	function polygon(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'POLYGON';
		return $this;		
	}
	
	function multipolygon(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'MULTIPOLYGON';
		return $this;		
	}
	
	function geometry(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'GEOMETRY';
		return $this;		
	}
	
	function geometrycollection(string $name){		
		$this->current_field = $name;
		$this->fields[$this->current_field] = [];
		$this->fields[$this->current_field]['type'] = 'GEOMETRYCOLLECTION';
		return $this;		
	}	
	
	// collation && charset 
	
	function collation(string $val){
		$this->fields[$this->current_field]['collation'] = $val;
		return $this;		
	}

	// alias
	function collate(string $val){
		$this->collation($val);
		return $this;		
	}
	
	function charset(string $val){
		$this->fields[$this->current_field]['charset'] = $val;
		return $this;		
	}
	
	/* 
		modifiers
	*/
	
	// autoincrement
	function auto(bool $val = true){
		$this->fields[$this->current_field]['auto'] =  $val;
		return $this;
	}

	function nullable(bool $value =  true){
		$this->fields[$this->current_field]['nullable'] =  $value ? 'NULL' : 'NOT NULL';
		return $this;
	}
	
	function comment($string){
		$this->fields[$this->current_field]['comment'] =  $string;
		return $this;
	}
	
	function default($val = NULL){
		if ($val === NULL) {
			$val = 'NULL';
		} elseif ($val === false) {
			$val = NULL;
		}

		$this->fields[$this->current_field]['default'] =  $val;
		return $this;
	}
	
	function dropDefault(){
		$this->fields[$this->current_field]['default'] =  NULL;
		return $this;
	}

	function currentTimestamp(){
		$this->default('current_timestamp()');	
		return $this;
	}
	
	protected function setAttr($attr){
		if (!in_array($attr, ['UNSIGNED', 'UNSIGNED ZEROFILL', 'BINARY'])){
			throw new \Exception("Attribute '$attr' is not valid.");
		}

		$this->fields[$this->current_field]['attr'] = $attr;
	}
	
	function unsigned(){
		$this->setAttr('UNSIGNED');
		return $this;
	}
	
	function zeroFill(){
		$this->setAttr('UNSIGNED ZEROFILL');
		return $this;
	}
	
	function binaryAttr(){
		$this->setAttr('BINARY');
		return $this;
	}
	
	// ALTER TABLE `aaa` ADD `ahora` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `fecha`;
	function onUpdateCurrent(){
		$this->setAttr('current_timestamp()');	
		return $this;
	}
	
	function after(string $field){
		$this->fields[$this->current_field]['after'] =  $field;
		return $this;
	}
	
	// ALTER TABLE `aaa` ADD `inicio` INT NOT NULL FIRST;
	function first(){
		if (isset($this->fields[$this->current_field]['after']))
			unset($this->fields[$this->current_field]['after']);
		
		foreach ($this->fields as $k => $field){
			if (isset($this->fields[$k]['first']))
				unset($this->fields[$k]['first']);
		}	
		
		$this->fields[$this->current_field]['first'] =  true;
		return $this;
	}
	
	// FKs
	
	function foreign(string $field_name){
		$this->current_field = $field_name;
		$this->fks[$this->current_field] = [];
		return $this;
	}

	// alias
	function fk(string $field_name){
		return $this->foreign($field_name);
	}
	
	function references(string $field_name){
		$this->fks[$this->current_field]['references'] = $field_name;
		return $this;
	}
	
	function on(string $table){
		$this->fks[$this->current_field]['on'] = $table;
		return $this;
	}
		
	function onDelete(string $action){
		$this->fks[$this->current_field]['on_delete'] = $action;
		return $this;
	}
	
	function onUpdate(string $action){
		$this->fks[$this->current_field]['on_update'] = $action;
		return $this;
	}

	function constraint(string $constraint_name){
		$this->fks[$this->current_field]['constraint'] = $constraint_name;
		return $this;
	}

	
	// INDICES >>>
	
	protected function setIndex(string $type){
		if (!in_array($type, ['PRIMARY', 'UNIQUE', 'INDEX', 'FULLTEXT', 'SPATIAL']))
			throw new \InvalidArgumentException("Invalid index $type");
		
		$this->indices[$this->current_field] = $type;
	}
	
	function primary(){
		$this->setIndex('PRIMARY');
		return $this;
	}
	
	function pri(){
		$this->primary();
		return $this;
	}
	
	function unique(){
		$this->setIndex('UNIQUE');
		return $this;
	}
	
	function index(){
		$this->setIndex('INDEX');
		return $this;
	}
	
	function fulltext(){
		$this->setIndex('FULLTEXT');
		return $this;
	}
	
	function spatial(){
		$this->setIndex('SPATIAL');
		return $this;
	}
	
	///////////////////////////////
	
	/*
		`nombre_campo` tipo[(longitud)] [(array_set_enum)] [charset] [collate] [attributos] NULL|NOT_NULL [default] [AUTOINCREMENT]
	*/
	function getDefinition($field){
		$cmd = '';		
		if (in_array($field['type'], ['SET', 'ENUM'])){
			$values = implode(',', array_map(function($e){ return "'$e'"; }, $field['array']));	
			$cmd .= "($values) ";
		}else{
			if (isset($field['len'])){
				$len = implode(',', (array) $field['len']);	
				$cmd .= "($len) ";
			}else
				$cmd .= " ";	
		}
		
		if (isset($field['attr'])){
			$cmd .= "{$field['attr']} ";
		}
		
		if (isset($field['charset'])){
			$cmd .= "CHARACTER SET {$field['charset']} ";
		}
		
		if (isset($field['collation'])){
			$cmd .= "COLLATE {$field['collation']} ";
		}
			
		if (isset($field['nullable'])){
			$cmd .= "{$field['nullable']} ";
		}else
			$cmd .= "NOT NULL ";

		if (isset($field['default'])){
			$cmd .= "DEFAULT {$field['default']} ";
		}

		if (isset($field['auto'])){
			$cmd .= "AUTO_INCREMENT PRIMARY KEY";
		}
		
		return trim($cmd);
	}

	private function showTable(){
		$conn = DB::getConnection();
		
		$stmt = $conn->query("SHOW CREATE TABLE `{$this->tb_name}`", \PDO::FETCH_ASSOC);
		$res  = $stmt->fetch();
		
		return $res;
	}
		
	// FOREIGN KEY (`abono_id`) REFERENCES `abonos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
	private function addFKs(){
		foreach ($this->fks as $name => $fk){
			$on_delete  = !empty($fk['on_delete'])  ? 'ON DELETE ' .$fk['on_delete']  : '';
			$on_update  = !empty($fk['on_update'])  ? 'ON UPDATE ' .$fk['on_update']  : '';
			$constraint = !empty($fk['constraint']) ? 'CONSTRAINT `'.$fk['constraint'].'`' : '';
			
			$this->commands[] = trim("ALTER TABLE  `{$this->tb_name}` ADD $constraint FOREIGN KEY (`$name`) REFERENCES `{$fk['on']}` (`{$fk['references']}`) $on_delete $on_update").';';
		}
	} 

	function createTable(){
		if ($this->tableExists()){
			throw new \Exception("Table {$this->tb_name} already exists");
		}

		if (empty($this->fields)){
			throw new \Exception("No fields!");
		}	

		if ($this->engine == NULL){
			throw new \Exception("Please specify table engine");
		}
		
		if ($this->charset == NULL){
			throw new \Exception("Please specify charset");
		}

		$this->commands = [
			'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";',
			/*
			'SET AUTOCOMMIT = 0;',
			'START TRANSACTION;',
			*/
			'SET time_zone = "+00:00";'
		];
	
		$cmd = '';
		foreach ($this->fields as $name => $field){
			$cmd .= "`$name` {$field['type']} ";			
			$cmd .= $this->getDefinition($field);	
			$cmd .= ",\n";
		}
		
		$cmd = substr($cmd,0,strlen($cmd)-2);
		
		$cmd = "CREATE TABLE `{$this->tb_name}` (\n$cmd\n) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset};";
		
		$this->commands[] = $cmd;
		
		// Indices
		
		if (count($this->indices) >0)
		{			
			$cmd = '';		
			foreach ($this->indices as $nombre => $tipo){
			
				switch ($tipo){
					case 'INDEX':
						$cmd .= "ADD INDEX (`$nombre`),\n";
					break;
					case 'PRIMARY':
						// PRIMARY can not be "ADDed"
					break;
					case 'UNIQUE':
						$cmd .= "ADD UNIQUE KEY `$nombre` (`$nombre`),\n";
					break;
					case 'SPATIAL':
						$cmd .= "ADD SPATIAL KEY `$nombre` (`$nombre`),\n";
					break;
					
					default:
						throw new \Exception("Invalid index type");
				}				
			}
			
			$cmd = substr($cmd,0,-2);
			$cmd = "ALTER TABLE `{$this->tb_name}` \n$cmd;";
			
			$this->commands[] = $cmd;
		}		
		
		
		// FKs		
		$this->addFKs();
				
		//$this->commands[] = 'COMMIT;';		
		$this->query = implode("\r\n",$this->commands)."\n";

		$conn = DB::getConnection();   
	  
		$rollback = function() use ($conn){
			$st = $conn->prepare("DROP TABLE IF EXISTS `{$this->tb_name}`;");
			$res = $st->execute();
		};

		try {
			foreach($this->commands as $change){     
				$st = $conn->prepare($change);
				$res = $st->execute();
			}

		} catch (\PDOException $e) {
			dd($change, 'SQL with error');
			dd($e->getMessage(), "PDO error");
			$rollback();
			throw $e;		
        } catch (\Exception $e) {
			$rollback();
            throw $e;
        } catch (\Throwable $e) {
            $rollback();
            throw $e;   
        }     

		return true;
	}

	// alias
	function create(){
		return $this->createTable();
	}
	
	function dropTable(){
		$this->commands[] = "DROP TABLE `{$this->tb_name}`;";
		return $this;
	}

	function dropTableIfExists(){
		$this->commands[] = "DROP TABLE IF EXISTS `{$this->tb_name}`;";
		return $this;
	}


	// TRUNCATE `az`.`xxy`
	function truncateTable(string $tb){
		$this->commands[] = "TRUNCATE `{$this->tb_name}`.`$tb`;";
		return $this;
	}


	// RENAME TABLE `az`.`xxx` TO `az`.`xxy`;
	function renameTable(string $final){
		$this->commands[] = "RENAME TABLE `{$this->tb_name}` TO `$final`;";
		return $this;
	}	


	function dropColumn(string $name){
		$this->commands[] = "ALTER TABLE `{$this->tb_name}` DROP `$name`;";
		return $this;
	}

	// https://popsql.com/learn-sql/mysql/how-to-rename-a-column-in-mysql/
	function renameColumn(string $ori, string $final){
		if ((int) $this->engine_ver >= 8){
			$this->commands[] = "ALTER TABLE `{$this->tb_name}` RENAME COLUMN `$ori` TO `$final`;";
		} else {
			if (!isset($this->prev_schema['fields'][$ori])){
				throw new \InvalidArgumentException("Column '$ori' does not exist in `{$this->tb_name}`");
			}

			$datatype = $this->prev_schema['fields'][$ori]['type'];

			if (isset($this->prev_schema['fields'][$this->current_field]['array'])){
				$datatype .= '(' . implode(',', $this->fields[$this->current_field]['array']). ')';
			} elseif (isset($this->prev_schema['fields'][$this->current_field]['len'])){
				$datatype .= '(' . $this->prev_schema['fields'][$this->current_field]['len'] . ')';
			} 

			$this->commands[] = "ALTER TABLE `{$this->tb_name}` CHANGE `$ori` `$final` $datatype;";
		}

		return $this;
	}
	
	// alias
	function renameColumnTo(string $final){		
		$this->commands[] = "ALTER TABLE `{$this->tb_name}` RENAME COLUMN `{$this->current_field}` TO `$final`;";
		return $this;
	}


	function addIndex(string $column){
		$this->commands[] = "ALTER TABLE `{$this->tb_name}` ADD INDEX(`$column`);";
		return $this;
	}

	function dropIndex(string $name){
		$this->commands[] = "ALTER TABLE `{$this->tb_name}` DROP INDEX `$name`;";
		return $this;
	}

	// https://stackoverflow.com/questions/1463363/how-do-i-rename-an-index-in-mysql
	function renameIndex(string $ori, string $final){
		$this->commands[] = "ALTER TABLE `{$this->tb_name}` RENAME INDEX `$ori` TO `$final`;";
		return $this;
	}

	// alias
	function renameIndexTo(string $final){
		$this->commands[] = "ALTER TABLE `{$this->tb_name}` RENAME INDEX `{$this->current_field}` TO `$final`;";
		return $this;
	}


	function addPrimary(string $column){
		$this->commands[] = "ALTER TABLE `{$this->tb_name}` ADD PRIMARY KEY(`$column`);";
		return $this;	
	}
		
	// implica primero remover el AUTOINCREMENT sobre el campo !
	// ej: ALTER TABLE `super_cool_table` CHANGE `id` `id` INT(11) NOT NULL;
	function dropPrimary(string $name){
		if ($this->prev_schema['fields'][$name]['auto']){
			throw new \Exception("To be able to DROP PRIMARY KEY, first remove AUTO_INCREMENT");
		}

		$this->commands[] = "ALTER TABLE `{$this->tb_name}` DROP PRIMARY KEY;";
		return $this;
	}


	function addUnique(string $column){
		$this->commands[] = "ALTER TABLE `{$this->tb_name}` ADD UNIQUE(`$column`);";
		return $this;
	}
		
	function dropUnique(string $name){
		$this->commands[] = $this->dropIndex($name);
		return $this;
	}



	function addSpatial(string $column){
		$this->commands[] = "ALTER TABLE ADD SPATIAL INDEX(`$column`);";
		return $this;
	}
		
	function dropSpatial(string $name){
		$this->commands[] = $this->dropIndex($name);
		return $this;
	}


	function dropForeign(string $name){
		$this->commands[] = "ALTER TABLE `{$this->tb_name}` DROP FOREIGN KEY `$name`";
		return $this;
	}

	// alias
	function dropFK(string $constraint_name){
		return $this->dropForeign($constraint_name);
	}


	// From DB 
	//
	protected function fromDB(){
		if (!in_array($this->tb_name, $this->tables)){
			return;
		}

		$table_def = $this->showTable();

		if ($table_def == NULL){
			throw new \Exception("[ Fatal error ] Table definition could not be recovered");
		}

		$lines = explode("\n", $table_def["Create Table"]);
		$lines = array_map(function($l){ return trim($l); }, $lines);
		
		$last_line     = $lines[count($lines) -1];
		$this->prev_schema['engine']  = Strings::slice($last_line, '/ENGINE=([a-zA-Z][a-zA-Z0-9_]+)/');
		$this->prev_schema['charset'] = Strings::slice($last_line, '/CHARSET=([a-zA-Z][a-zA-Z0-9_]+)/');

		$fields = [];
		$cnt = count($lines)-1;
		for ($i=1; $i<$cnt; $i++){
			$str = $lines[$i];
			
			if ($lines[$i][0] == '`')
			{
				$field 		= NULL;
				$type  		= NULL;
				$array		= NULL;				
				$len   		= NULL;
				$charset  	= NULL;
				$collation 	= NULL;
				$nullable	= NULL;
				$default	= NULL;
				$auto 		= NULL;
				$check 		= NULL;
				
				$field      = Strings::slice($str, '/`([a-z_]+)`/i');
				$type       = Strings::slice($str, '/([a-z_]+)/i');

				$this->raw_lines[$field] = $lines[$i];

				if ($type == 'enum' || $type == 'set'){
					$array = Strings::slice($str, '/\((.*)\)/i');
				}else{
					$len = Strings::slice($str, '/\(([0-9,]+)\)/');					
				}


				$charset    = Strings::slice($str, '/CHARACTER SET ([a-z0-9_]+)/');
				$collation  = Strings::slice($str, '/COLLATE ([a-z0-9_]+)/');
				
				$default    = Strings::slice($str, '/DEFAULT ([a-zA-Z0-9_\(\)]+)/');
				//dd($default, "DEFAULT($field)");

				$nullable   = Strings::slice($str, '/(NOT NULL)/') == NULL;
				$auto       = Strings::slice($str, '/(AUTO_INCREMENT)/') == 'AUTO_INCREMENT';
				//dd($nullable, "NULLABLE($field)");


				
				// [CONSTRAINT [symbol]] CHECK (expr) [[NOT] ENFORCED]	
				$check   = Strings::sliceAll($str, '/CHECK \((.*)\) (ENFORCED|NOT ENFORCED)/');
					
				//if (strlen($str)>1)
				//	throw new \Exception("Parsing error!");				
			
				
				/*
				dd($field, 'FIELD ***');
				dd($lines[$i], 'LINES');
				dd($type, 'TYPE');
				dd($array, 'ARRAY / SET');
				dd($len, 'LEN');
				dd($charset, 'CHARSET');
				dd($collation, 'COLLATION');
				dd($nullable, 'NULLBALE');
				dd($default, 'DEFAULT');
				dd($auto, 'AUTO');
				dd($check, 'CHECK');
				echo "-----------\n";
				*/
								

				$this->prev_schema['fields'][$field]['type'] = strtoupper($type);
				$this->prev_schema['fields'][$field]['auto'] = $auto; 
				//$this->prev_schema['fields'][$field]['attr'] = ...
				$this->prev_schema['fields'][$field]['len'] = $len;
				$this->prev_schema['fields'][$field]['array'] = $array;
				$this->prev_schema['fields'][$field]['nullable'] = $nullable;
				$this->prev_schema['fields'][$field]['charset'] = $charset;
				$this->prev_schema['fields'][$field]['collation'] = $collation;
				$this->prev_schema['fields'][$field]['default'] = $default;
				// $this->prev_schema['fields'][$field]['after'] =  ...
				// $this->prev_schema['fields'][$field]['first'] = ...

			}else{
				// son índices de algún tipo
				//dd($str);
				
				$primary = Strings::slice($str, '/PRIMARY KEY \(`([a-zA-Z0-9_]+)`\)/');				
				$unique  = Strings::sliceAll($str, '/UNIQUE KEY `([a-zA-Z0-9_]+)` \(`([a-zA-Z0-9_]+)`\)/');
				$index   = Strings::sliceAll($str, '/KEY `([a-zA-Z0-9_]+)` \(`([a-zA-Z0-9_]+)`\)/');
				
				/*
				dd($primary);
				dd($unique);
				dd($index);
				echo "-----------\n";
				*/	
				
				/*
				if ($primary != NULL){
					$this->prev_schema['indices'][$field] = 'PRIMARY';
				} elseif ($unique != NULL){
					$this->prev_schema['indices'][$field] = 'UNIQUE';
				}if ($index != NULL){
					$this->prev_schema['indices'][$field] = 'INDEX';
				}
				*/
			}
		}
		
	}

	function dd(){
		return $this->query;
	}
	
	// ALTER TABLE `users` CHANGE `lastname` `lastname` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
	
	// ALTER TABLE `users` CHANGE `id` `id` INT(20) UNSIGNED NOT NULL;
	function change()
	{	
		foreach ($this->fields as $name => $field)
		{
			if (isset($this->prev_schema['fields'][$name])){
				$this->fields[$name] = array_merge($this->prev_schema['fields'][$name], $this->fields[$name]);
			} 		

			/*
			if ($name == 'vencimiento'){
				dd($this->prev_schema['fields'][$name]['nullable']);
				dd($this->fields[$name]);
				exit;
			}
			*/
			
			$this->indices[$name] = $this->prev_schema['indices'][$name] ?? NULL;
			
			$field = $this->fields[$name];

			//dd($this->fields[$name]);
			//exit;

			$charset   = isset($field['charset']) ? "CHARACTER SET {$field['charset']}" : '';
			$collation = isset($field['collation']) ? "COLLATE {$field['collation']}" : '';
			
			$def = "{$this->fields[$name]['type']}";		
			if (in_array($field['type'], ['SET', 'ENUM'])){
				$values = implode(',', array_map(function($e){ return "'$e'"; }, $field['array']));	
				$def .= "($values) ";
			}else{
				if (isset($field['len'])){
					$len = implode(',', (array) $field['len']);	
					$def .= "($len) ";
				}else
					$def .= " ";	
			}
			
			if (isset($field['attr'])){
				$def .= "{$field['attr']} ";
			}
			
			if (in_array($field['type'], ['CHAR', 'VARCHAR', 'TEXT', 'TINYTEXT', 'MEDIUMTEXT', 'LONGTEXT', 'JSON', 'SET', 'ENUM'])){
				$def .= "$charset $collation ";	
			}		
			
			if (isset($field['nullable']) && $field['nullable'] == 'NULL'){  
				$def .= "NULL ";
			} else {		
				$def .= "NOT NULL ";
			}	

			/*			
			dd($field['nullable'], "NULLABLE ($name)");
			dd($field['default'], "DEFAULT ($name)");
			exit;
			*/

			if (isset($field['nullable']) && !$field['nullable'] && isset($field['default']) && $field['default'] == 'NULL'){
				throw new \Exception("Column `$name` can not be not nullable but default 'NULL'");
			}
				
			if (isset($field['default'])){
				$def .= "DEFAULT {$field['default']} ";
			}
			
			if (isset($field['auto']) && $field['auto'] === false){
				$def = str_replace('AUTO_INCREMENT', '', $def);
			}
			
			if (isset($field['after'])){  
				$def .= "AFTER {$field['after']}";
			} elseif (isset($field['first'])){
				$def .= "FIRST ";
			}

			$def = trim(preg_replace('!\s+!', ' ', $def));
			

			if (isset($this->prev_schema['fields'][$name])){
				$this->commands[] = "ALTER TABLE `{$this->tb_name}` CHANGE `$name` `$name` $def;";
			} else {
				$this->commands[] = "ALTER TABLE `{$this->tb_name}` ADD `$name` $def;";
			}	
		
		}

		foreach ($this->indices as $name => $type){			
			switch($type){
				case "INDEX":
					$this->addIndex($name);
				break;
				case "PRIMARY":
					$this->addPrimary($name);
				break;
				case "UNIQUE": 
					$this->addUnique($name);
				break;
				case "SPATIAL": 
					$this->addSpatial($name);
				break;
			}
		}

		// FKs
		$this->addFKs();


		$this->query = implode("\r\n",$this->commands);
	
		$conn = DB::getConnection();   

		
		DB::beginTransaction();
		try{
			foreach($this->commands as $change){     
				$st = $conn->prepare($change);
				$res = $st->execute();
			}

			DB::commit();
		} catch (\PDOException $e) {
			DB::rollback();
			dd($change, 'SQL');
			dd($e->getMessage(), "PDO error");		
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollback();            
        }     
	}	
	
	// reflexion
	
	function getSchema(){
		return [
			'engine'	=> $this->engine,
			'charset'	=> $this->charset,
			'collation'	=> $this->collation,
			'fields'	=> $this->fields,
			'indices'	=> $this->indices,
			'fks'		=> $this->fks
		];
	}

	function getCurrentSchema(){
		return $this->prev_schema;
	}
}


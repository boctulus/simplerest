<?php

namespace simplerest\libs;

use simplerest\core\Model;
use simplerest\libs\Factory;
use simplerest\libs\Strings;

class DB 
{
	protected static $connections = [];
	protected static $current_id_conn;
	protected static $model_instance;  
	protected static $raw_sql;

	protected function __construct() { }
	
	public static function getCurrentConnectionId(){
		return static::$current_id_conn;
	}

	public static function getCurrent(){
		if (static::$current_id_conn === null){
			return null;
		}

		return Factory::config()['db_connections'][static::$current_id_conn];
	}

	public static function database(){
		$current = self::getCurrent();

		if ($current === null){
			return null;
		}
		
		return self::getCurrent()['db_name'];
	}

	// alias
	public static function getCurrentDB(){
		return self::database();
	}

	public static function driver(){
		return self::getCurrent()['driver'];
	}

	public static function schema(){
		return self::getCurrent()['schema'] ?? NULL;
	}
	
	public static function setConnection($id){
		static::$current_id_conn = $id;
	}

    public static function getConnection(string $conn_id = null) {
		$config = Factory::config();
		
		$cc = count($config['db_connections']);
		
		if ($cc == 0){
			throw new \Exception('No database');
		}

		if ($conn_id != null){
			static::$current_id_conn = $conn_id;	
		} else {
			if (static::$current_id_conn == null){
				if ($cc == 1){
					static::$current_id_conn = array_keys($config['db_connections'])[0];
				} elseif (!empty($config['db_connection_default'])) {
					static::$current_id_conn = $config['db_connection_default'];
				} else {	
					throw new \InvalidArgumentException('No database selected');
				}	
			}
		}

		if (isset(self::$connections[static::$current_id_conn]))
			return self::$connections[static::$current_id_conn];

		
		if (!isset($config['db_connections'][static::$current_id_conn])){
			throw new \InvalidArgumentException('Invalid database selected for '.static::$current_id_conn);
		}	
		
		$host    = $config['db_connections'][static::$current_id_conn]['host'] ?? 'localhost';
		$driver  = $config['db_connections'][static::$current_id_conn]['driver'];	
		$port    = $config['db_connections'][static::$current_id_conn]['port'] ?? NULL;
        $db_name = $config['db_connections'][static::$current_id_conn]['db_name'];
		$user    = $config['db_connections'][static::$current_id_conn]['user'] ?? 'root';
		$pass    = $config['db_connections'][static::$current_id_conn]['pass'] ?? '';
		$pdo_opt = $config['db_connections'][static::$current_id_conn]['pdo_options'] ?? NULL;
		$charset = $config['db_connections'][static::$current_id_conn]['charset'] ?? NULL;

		// alias
		if ($driver == 'postgres'){
			$driver = 'pgsql';
		}
		
		try {
			switch ($driver) {
				case 'mysql':
					self::$connections[static::$current_id_conn] = new \PDO("$driver:host=$host;dbname=$db_name;port=$port", $user, $pass, $pdo_opt);				
					break;
				case 'sqlite':
					$db_file = Strings::contains(DIRECTORY_SEPARATOR, $db_name) ?  $db_name : STORAGE_PATH . $db_name;
	
					self::$connections[static::$current_id_conn] = new \PDO("sqlite:$db_file", null, null, $pdo_opt);
					break;

				case 'pgsql':
					self::$connections[static::$current_id_conn] = new \PDO("$driver:host=$host;dbname=$db_name;port=$port", $user, $pass, $pdo_opt);
					break;	

				default:
					throw new \Exception("Driver '$driver' not supported / tested.");
			}


			if ($charset != null){
				self::$connections[static::$current_id_conn]->exec("SET NAMES 'UTF8'");	
			}	

		} catch (\PDOException $e) {
			throw new \PDOException('PDO Exception: '. $e->getMessage());	
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}	
		
		return self::$connections[static::$current_id_conn];
	}
	
    static function closeConnection(string $conn_id = null) {
		if ($conn_id == null){
			unset(static::$connections[static::$current_id_conn]);
			static::$current_id_conn = NULL; // undefined
		} else {
			static::$connections[$conn_id] = null;
		}
		//echo 'Successfully disconnected from the database!';
	}

	static function closeAllConnections(){
		static::$connections = null;
	}
	
	public function __destruct()
    {
        static::closeAllConnections();        
    }
	
	public static function countConnections(){
		return count(static::$connections ?? []);
	}

	// Returns last executed query 
	public static function getLog(){
		if (static::$model_instance != NULL){
			return static::$model_instance->getLog();
		} else {
			return static::$raw_sql;
		}
	}
	
	public static function table($from, $alias = NULL, bool $connect = true) {
		// Usar un wrapper y chequear el tipo
		if (!Strings::contains(' FROM ', $from))
		{
			$names = explode('_', $from);
			$names = array_map(function($str){ return ucfirst($str); }, $names);
			$model_instance = implode('', $names).'Model';		

			$class = '\\simplerest\\models\\' . $model_instance;
			$obj = new $class($connect);

			if (!is_null($alias))
				$obj->setTableAlias($alias);

			static::$model_instance = $obj;			
			return $obj;	
		}

		static::$model_instance = (new Model($connect));

		$st = static::$model_instance->fromRaw($from);	
		return $st;
	}

	public static function beginTransaction(){
		/* 
		  Not much to it! Forcing PDO to throw exceptions instead errors is the key to being able to use the try / catch which simplifies the logic needed to perform the rollback.
		*/
		static::getConnection()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		static::getConnection()->beginTransaction();
	}

	public static function commit(){
		static::getConnection()->commit();
	}

	public static function rollback(){
		static::getConnection()->rollback();
	}

	// https://github.com/laravel/framework/blob/4.1/src/Illuminate/DB/Connection.php#L417
	public static function transaction(\Closure $callback)
    {
		static::beginTransaction();

		try
		{
			$result = $callback();
			static::commit();
		}catch (\Exception $e){
			static::rollBack();
			throw $e;
		}

		return $result;
    }
		
	// falta hacer que acepte parámetros
	//
	// https://laravel.com/docs/5.0/database
	//
	public static function select(string $raw_sql, $fetch_mode = 'ASSOC'){
		static::$raw_sql = $raw_sql; 

		$conn = DB::getConnection();
        
        $st = $conn->prepare($raw_sql);
        $st->execute();

		$fetch_const = constant("\PDO::FETCH_{$fetch_mode}");
		$result = $st->fetchAll($fetch_const);
		return $result;
	}

	// faltan otras funciones raw para DELETE, UPDATE e INSERT

}

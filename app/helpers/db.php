<?php

use simplerest\libs\Strings;
use simplerest\libs\DB;
use simplerest\core\Model;

/*
    Example about how to get database connections dinamically
*/
function get_db_connections()
{
    static $connections = [];

    if (!empty($connections)){
        return $connections;
    }

    /*
        Settings
    */

    $driver = 'mysql';
    $host   = '127.0.0.1';
    $port   = 3306;
    $dbname = 'db_admin_dsi';
    $user = 'boctulus';
    $pass = 'gogogo#*$U&_441@#';
    $charset = 'utf8';
    
    $dsn  =  "$driver:dbname=$dbname;port=$port;host=$host";

    try {
        $conn = new \PDO($dsn, $user, $pass);
    } catch (PDOException $e) {
        throw new \Exception('DB connection fails: ' . $e->getMessage());
    }

    $m = (new Model(false, null, false))
    ->setConn($conn);
    
    $bases = $m->table('tbl_base_datos')
    ->pluck('dba_varNombre');

    $connections = [
        // Main DB connection
        'main' => [
			'host'		=> $host,
			'port'		=> $port,
			'driver' 	=> $driver,
			'db_name' 	=> 'db_admin_dsi',
			'user'		=> $user, 
			'pass'		=> $pass,
			'charset'	=> $charset, 
			'pdo_options' => [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]
		]
    ];

    if (!empty($bases)){
        // other connections
        foreach ($bases as $base){
            $connections[$base] = [
                'host'		=> $host,
                'port'		=> $port,
                'driver' 	=> $driver,
                'db_name' 	=> $base,
                'user'		=> $user, 
                'pass'		=> $pass,
                'charset'	=> $charset, 
                'pdo_options' => [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ]
            ];
        }
    }

    return $connections;
}

function get_user_model_name(){
    return '\\simplerest\\models\\' . Strings::snakeToCamel(config()['users_table']). 'Model';
}

function get_model_name($table_name){
    return '\\simplerest\\models\\' . Strings::snakeToCamel($table_name).  'Model';
}

function get_schema_name($table_name, $tenant_id = null){
    if ($tenant_id !== null){
        DB::getConnection($tenant_id); 
    }    
      
    if (DB::getCurrentConnectionId() == null || DB::getCurrentConnectionId() == config()['db_connection_default']){
        $extra = config()['db_connection_default'] . '\\';
    } else {
        $extra = '';
    }

    return '\\simplerest\\models\\schemas\\' . $extra . Strings::snakeToCamel($table_name). 'Schema';
}

function inSchema(array $props, string $table_name){
    $class = get_schema_name($table_name);

    if (!class_exists($class)){
        throw new \Exception("Class $class does not exist");
    }
   
    $attributes = array_keys($class::get()['attr_types']);

	if (empty($props))
		throw new \InvalidArgumentException("Attributes not found!");

	foreach ($props as $prop)
		if (!in_array($prop, $attributes)){
			return false; 
		}	
	
	return true;
}

function get_name_id(string $table_name, $tenant_id = null){
    $class = get_schema_name($table_name, $tenant_id);

    if (!class_exists($class)){
        throw new \Exception("Class $class does not exist");
    }

    return $class::get()['id_name'];
}

/*
    Devuelve un path que puede usarse para crear inyectar una migración "manualmente"
*/
function generateMigrationFileName($tb_name){
        
    // 2020_10_28_141833_yyy
    $date = date("Y_m_d");
    $secs = time() - 1603750000;
    $filename = $date . '_'. $secs . '_' . Strings::camelToSnake($tb_name) . '.php'; 

    // destination
    return MIGRATIONS_PATH . $filename;
}

/*
    Lee linea por línea y ejecuta sentencias SQL
*/
function readSql(string $path){
    $file = file_get_contents($path);
    
    $sentences = explode(';', $file);
        
    foreach ($sentences as $sentence){
        $sentence = trim($sentence);

        if ($sentence == ''){
            continue;
        }

        dd($sentence, 'SENTENCE');

        try {
            $ok = Model::query($sentence);
        } catch (\Exception $e){
            dd($e, 'Sql Exception');
        }
    }    
}
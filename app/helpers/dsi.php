<?php

use simplerest\libs\Strings;
use simplerest\libs\DB;
use simplerest\core\Model;
use simplerest\libs\Factory;

include_once __DIR__ . '/debug.php';


function get_db_connections()
{
    static $connections = [];

    if (!empty($connections)){
        return $connections;
    }

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
        // cargo conexión principal
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

    // cargo resto de conexiones
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

    return $connections;
}
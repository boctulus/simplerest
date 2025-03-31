<?php

use Boctulus\Simplerest\Core\Libs\Env;
use Boctulus\Simplerest\Core\Model;

#include_once __DIR__ . '/debug.php';

/*
    Example about how to get database connections dinamically
*/

function get_db_connections(bool $avoid_cache = false)
{
    static $connections = [];

    if ($avoid_cache === false && !empty($connections)){
        return $connections;
    }

    /*
        Settings
    */

    // $driver = env('DB_CONNECTION_REMOTE');
    // $host   = env('DB_HOST_REMOTE');;
    // $port   = env('DB_PORT_REMOTE');
    // $dbname = env('DB_NAME_REMOTE');
    // $user   = env('DB_USERNAME_REMOTE');
    // $pass   = env('DB_PASSWORD_REMOTE');   
    

    $driver = env('DB_CONNECTION');
    $host   = env('DB_HOST');;
    $port   = env('DB_PORT');
    $dbname = env('DB_NAME');
    $user   = env('DB_USERNAME');
    $pass   = env('DB_PASSWORD');

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
				#\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false,
                #\PDO::ATTR_AUTOCOMMIT => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode="TRADITIONAL"'
			]
        ],

        //  Para pruebas locales de Pablo
        'az' => [
            'host'		=> '127.0.0.1',
            'port'		=> 3306,
            'driver' 	=> 'mysql',
            'db_name' 	=> 'az',
            'user'		=> 'boctulus', 
            'pass'		=> 'gogogo#*$U&_441@#',
            'charset'	=> 'utf8', 
            'pdo_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                #\PDO::ATTR_AUTOCOMMIT => false
            ]
        ],
        
        //  Para pruebas locales de Pablo
        'db_flor' => [
            'host'		=> '127.0.0.1',
            'port'		=> 3306,
            'driver' 	=> 'mysql',
            'db_name' 	=> 'db_flor',
            'user'		=> 'boctulus', 
            'pass'		=> 'gogogo#*$U&_441@#',
            'charset'	=> 'utf8', 
            'pdo_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                #\PDO::ATTR_AUTOCOMMIT => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode="TRADITIONAL"'
            ]
        ],

        //  Docker de Pablo con MariaDB
        'db_admin_mariadb_pablo' => [
            'host'		=> '127.0.0.1',
            'port'		=> 43306,
            'driver' 	=> 'mysql',
            'db_name' 	=> 'db_admin_dsi',
            'user'		=> 'boctulus', 
            'pass'		=> 'gogogo2k',
            'charset'	=> 'utf8', 
            'pdo_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                #\PDO::ATTR_AUTOCOMMIT => false
            ]
        ],

        //  Docker de Pablo (otro)
        'db_docker_php81_mysql' => [
            'host'		=> '127.0.0.1',
            'port'		=> 43307,
            'driver' 	=> 'mysql',
            'db_name' 	=> 'db_test',
            'user'		=> 'boctulus', 
            'pass'		=> 'gogogo2k',
            'charset'	=> 'utf8', 
            'pdo_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                #\PDO::ATTR_AUTOCOMMIT => false
            ]
        ],
        
        // 
        'hogar' => [
            'host'		=> '127.0.0.1',
            'port'		=> 3306,
            'driver' 	=> 'mysql',
            'db_name' 	=> 'letotoncasa_pm',
            'user'		=> 'boctulus', 
            'pass'		=> 'gogogo#*$U&_441@#',
            'charset'	=> 'utf8', 
            'pdo_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                #\PDO::ATTR_AUTOCOMMIT => false
            ]
        ],
    ];

    // other connections
    if (!empty($bases)){
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
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    //\PDO::ATTR_AUTOCOMMIT => false,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode="TRADITIONAL,ALLOW_INVALID_DATES"'
                ]
            ];
        }
    }

    return $connections;
}
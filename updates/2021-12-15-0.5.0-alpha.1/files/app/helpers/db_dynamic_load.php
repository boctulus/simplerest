<?php

use simplerest\libs\Env;
use simplerest\core\Model;

include_once __DIR__ . '/debug.php';

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
    $host   = '190.29.102.172';
    $port   = 3315;
    $dbname = 'db_admin_dsi';
    $user = 'root';
    $pass = 'Disc9#math0';


    // $driver = 'mysql';
    // $host   = '127.0.0.1';
    // $port   = 3306;
    // $dbname = 'db_admin_dsi';
    // $user = 'root';
    // $pass = '';


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
            'host'      => $host,
            'port'      => $port,
            'driver'    => $driver,
            'db_name'   => 'db_admin_dsi',
            'user'      => $user, 
            'pass'      => $pass,
            'charset'   => $charset, 
            'pdo_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                #\PDO::ATTR_AUTOCOMMIT => false
            ]
        ],

        /* others connections */
    ];

    if (!empty($bases)){
        foreach ($bases as $base){
            $connections[$base] = [
                'host'      => $host,
                'port'      => $port,
                'driver'    => $driver,
                'db_name'   => $base,
                'user'      => $user, 
                'pass'      => $pass,
                'charset'   => $charset, 
                'pdo_options' => [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    //\PDO::ATTR_AUTOCOMMIT => false
                ]
            ];
        }
    }

    return $connections;
}
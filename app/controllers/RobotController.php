<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\libs\Env;
use simplerest\core\Response;
use simplerest\core\libs\HTTP;
use simplerest\core\libs\Config;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyController;

class RobotController extends MyController
{
    protected $robot_path;

    function __construct()
    {
        parent::__construct(); 
        HTTP::cors();

        $this->robot_path = Env::get('ROBOT_PATH');
    }

    protected function setupConnection(){
        Config::set('db_connections.robot', [            
            'host'		=> env('DB_HOST', '127.0.0.1'),
            'port'		=> env('DB_PORT'),
            'driver' 	=> env('DB_CONNECTION'),
            'db_name' 	=> 'robot',
            'user'		=> env('DB_USERNAME'), 
            'pass'		=> env('DB_PASSWORD'),
            'charset'	=> 'utf8',
            'pdo_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        ]);

        DB::getConnection('robot');
    }

    /*
        Crea una orden a ejecutar
    */
    function order(){
        try {
            $raw   = file_get_contents("php://input");
            $order = json_decode($raw, true);

            $file  = time() . '-' . rand(100,999);

            file_put_contents($this->robot_path . "/instructions/$file.json", $raw);

            $res = Response::getInstance();

            $res->sendJson([
                "message"  => "Orden puesta para ejecucion",                
                "order"    => $order,
                "filename" => $file
            ]);

        } catch (\Exception $e){
            $res->error($e->getMessage());
        }
    }   

    /*
        Retorna status del robot
    */
    function status(){
        try {
            $this->setupConnection();

            $rows = table('robot_execution')
            ->orderBy(['id' => 'DESC'])
            ->getOne();

            $res = Response::getInstance();
            $res->sendJson($rows);

        } catch (\Exception $e){
            $res->error($e->getMessage());
        }
    }    

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }
}


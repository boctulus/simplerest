<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Libs\Env;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\HTTP;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\System;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Modules\robot\Robot;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Logger;

class RobotController extends Controller
{
    protected $robot_path;

    function __construct()
    {
        parent::__construct(); 
        cors();

        $this->robot_path = Env::get('ROBOT_PATH');
    }

   function index()
    {
        new Robot();                
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

        TO-DO

        - La crea pero la debe poner en ejecucion invocando a Python con el script y el archivo de instrucciones
    */
    function order(){
        try {
            $raw   = file_get_contents("php://input");
            $order = json_decode($raw, true);

            $file  = 'order-' . ((string) time()) . '-' . ((string) rand(100,999)) . '.json';            
            $path  = $this->robot_path . "/instructions/$file";

            file_put_contents($path, $raw);

            $pid = System::execInBackgroundWindows(Env::get('PYTHON_BINARY'), Env::get('ROBOT_PATH'), " index.py $file", true);

            $res = Response::getInstance();

            $res->sendJson([
                "message"  => "Orden puesta para ejecucion",                
                "order"    => $order,
                "filename" => $file,
                "PID"      => $pid
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
            $res = Response::getInstance();

            $this->setupConnection();

            $rows = table('robot_execution')
            ->orderBy(['id' => 'DESC'])
            ->getOne();
           
            $res->sendJson($rows);

        } catch (\Exception $e){
            $res->error($e->getMessage());
        }
    }    

    function screenshots($filename){
        try {
            $res = Response::getInstance();

            $path = $this->robot_path . "/screenshots/$filename";

            if (!file_exists($path)){
                http_response_code(404);
                $res->error('File not found', 404);
            }
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($path).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;

        } catch (\Exception $e){
            $res->error($e->getMessage());
        }
    }

    function test_py_bg()
    {   
        $pid = System::execInBackgroundWindows(Env::get('PYTHON_BINARY'), Env::get('ROBOT_PATH'), " index.py pablotol.py", true);

        dd($pid, 'PID');
    }
}


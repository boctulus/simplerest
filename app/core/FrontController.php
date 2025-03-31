<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\api\v1\ApiController;
use Boctulus\Simplerest\Core\Controllers\ConsoleController;
use Boctulus\Simplerest\Core\Libs\Cli;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Msg;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Libs\VarDump;

class FrontController
{
    const DEFAULT_ACTION = "index";

    static function resolve()
    {
        global $argv;
        global $api_version;

        $config      = Config::get();
        $middlewares = include CONFIG_PATH . 'middlewares.php';
        
        $res = Response::getInstance();    
        $sub = (int) $config['remove_api_slug'];

        if (php_sapi_name() != 'cli'){
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = preg_replace('/(.*)\/index.php/', '/', $path);
    
            /*
                La idea es tener ciertas rutas relativas para las cuales no se intente interpretar como Controller
            */

            $allowed_paths = [
                '/app/views/',
                '/app/shortcodes/'
            ];

            foreach($allowed_paths as $ok_path){
                if (Strings::startsWith($ok_path, $path)){
                    $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
                    $path = Files::removeTrailingSlash(ROOT_PATH) . $path;

                    include $path;

                    // evito siga el flujo normal
                    return;
                }
            }

            $config['base_url'] = Files::addTrailingSlash($config['base_url']);

            if ($config['base_url'] != '/' && strpos($path, $config['base_url']) === 0) {
                $path = substr($path, strlen($config['base_url']));
            }   
    
            if ($path === false || ! Url::urlCheck($_SERVER['REQUEST_URI']) ){
                $res->error(Msg::MALFORMED_URL, 400); 
            }
    
            $_params = explode('/', $path);
    
            if (empty($_params[0]))  
                array_shift($_params);
        }else{
            $_params = array_slice($argv, 1);
        }
        
        if (!isset($_params[0])){
            return; // *
        }


        $req = Request::getInstance(); 

        $is_auth = ((!$config['remove_api_slug'] && isset($_params[2]) && $_params[2] === 'auth') || ($config['remove_api_slug'] && $_params[1] == 'auth'));
        $sub = (int) $config['remove_api_slug'];

        if ($is_auth){
            $namespace = 'Boctulus\Simplerest\\Controllers\\';

            $class_file =  CONTROLLERS_PATH. 'MyAuthController.php';
        
            $action = $_params[3 - $sub] ?? null;  
            $params = array_slice($_params, 4 - $sub);
            $req->setParams($params);                    

            $method = !empty($action) ? $action : self::DEFAULT_ACTION;
    
            $class_name = 'MyAuth';
            $class_name = "{$namespace}{$class_name}Controller";

            // dd($class_name, 'CLASS_NAME:');
            // dd($method, 'METHOD:');

            $api_version = $_params[1 - $sub]; 

            if (!preg_match('/^v[0-9]+(\.+[0-9]+)?$/', $api_version, $matches) ){
                $res->error(Msg::INVALID_FORMAT_API_VERSION['text']);
            }

        } elseif ($_params[0] === 'api' || $config['remove_api_slug']) {
            if (!isset($_params[1 - $sub])){
                $res->error(Msg::MISSING_API_VERSION['text']);
            }

            $api_version = $_params[1 - $sub]; 

            if (!preg_match('/^v[0-9]+(\.+[0-9]+)?$/', $api_version, $matches) ){
                $res->error(Msg::INVALID_FORMAT_API_VERSION['text']);
            }

            $controller = $_params[2 - $sub] ?? NULL;  
                
            // CamelCase to came_case
            $controller = implode('',array_map('ucfirst',explode('_',$controller)));
        
            if ($controller == 'trash_can' || $controller == 'trashCan' || $controller == 'TrashCan' || $controller == 'collections' || $controller == 'Collections') {
                $namespace = 'simplerest\\core\\api\\'. $api_version . '\\';
            } else {
                $namespace = 'Boctulus\Simplerest\\Controllers\\api\\';
            }    

            $class_name = $namespace . ucfirst($controller); //
            
            $method = strtolower($req->method());
            $params = array_slice($_params,3 - $sub,2);  // *
            

            $req->setParams($params);  

        } else {
            //dd($_params, 'PARAMS:');

            $namespace = 'Boctulus\Simplerest\\Controllers\\';

            if (empty($_params) || $_params[0]==''){
                $class_file = substr($config['default_controller'],0, strlen($config['default_controller'])-10);
                $class_name = Strings::snakeToCamel($class_file);
                $class_name = "{$namespace}{$class_name}Controller";
                $method = self::DEFAULT_ACTION;  
                $params = []; 
            }else{
                // Hipótesis
                $ix = 0;
                $folder = '';
                $controller = $_params[$ix];

                $class_file =  CONTROLLERS_PATH.Strings::snakeToCamel($controller).'Controller.php';
                $cnt  = count($_params) -1;
                while (!file_exists($class_file) && ($ix < $cnt)){
                    $ix++;
                    $folder = implode(DIRECTORY_SEPARATOR, array_slice($_params,0,$ix)). DIRECTORY_SEPARATOR;
                    //dd($folder, 'NAMESPACE:');

                    if (is_numeric($_params[$ix])){
                        break;
                    }

                    $controller = $_params[$ix];
                    $class_file =  CONTROLLERS_PATH. $folder. Strings::snakeToCamel($controller).'Controller.php';;
                }

                //dd($class_file, "Probando ...");
                
                $action = $_params[$ix+1] ?? null;
                $params = array_slice($_params,$ix+2);
                $req->setParams($params);                    

                $method = !empty($action) ? $action : self::DEFAULT_ACTION;
        
                $class_name = Strings::snakeToCamel($controller);
                $class_name = "{$namespace}{$folder}{$class_name}Controller";

                //dd($class_name, 'CLASS_NAME:');
                //dd($method, 'METHOD:');
            }
        }

        
        $class_name = str_replace('/', "\\", $class_name);

        // error_log(var_export([
        //     'class' => $class_name,
        //     'method' => $method,
        //     'params' => $params
        // ], 1));

        if (!class_exists($class_name)){
            $res->error(Msg::CLASS_NOT_FOUND, 404, "Internal error - controller class $class_name not found"); 
        } 

       
        if (!method_exists($class_name, $method)){
            if (php_sapi_name() != 'cli' || $method != self::DEFAULT_ACTION){
                /*
                    Se agrego is_callable() para poder usarse con __call()
                */
                if (!method_exists($class_name, '__call') || !is_callable($class_name, $method)){
                    $res->error("Internal error - method $method was not found in $class_name", 404); 
                }
            } else {
                $dont_exec = true;
            }
        }
                   
        $controller_obj = new $class_name();

        if (isset($dont_exec)){
            exit;
        }

        // Only for API Rest
        if ($_params[0] === 'api' && !$is_auth){
            if (!in_array($method, $controller_obj->getCallable())){
                $res->error("Not authorized for $controller:$method", 403);
            }
        }
        
        $data = call_user_func_array([$controller_obj, $method], $params);

        if ($data !== null){
            // Determinar formato de salida
            $output_format = $controller_obj->getOutputFormat();
            
            if ($output_format === 'test' && Request::isBrowser()) {
                $output_format = 'dd';
            } else {
                $output_format = 'auto';
            }

            if ($output_format === 'auto') {
                // Lógica automática
                if ($controller_obj instanceof ApiController) {
                    $output_format = 'json';
                } elseif ($controller_obj instanceof ConsoleController) {
                    $output_format = 'dd';
                } elseif (Url::isPostmanOrInsomnia()) {
                    $output_format = 'pretty_json';
                } else {
                    $output_format = 'dd';
                }
            }

            // Aplicar formato
            switch ($output_format) {
                case 'json':
                    $res->setHeader('Content-Type', 'application/json');
                    if (!Strings::isJSON($data)){
                        $data = json_encode($data);
                    }                   
                    break;
                    
                case 'pretty_json':
                    $res->setHeader('Content-Type', 'application/json');
                    if (!Strings::isJSON($data)){
                        $data = json_encode($data, JSON_PRETTY_PRINT);
                    }  
                    break;
                    
                case 'dd':
                    if (php_sapi_name() === 'cli') {
                        $data = Cli::formatOutput($data, 0, true);
                    } else {                    
                        $data = Strings::formatOutput($data);                    
                    }
                    break;
            }

            // Devolver algo desde un controlador sería equivalente a enviarlo como respuesta
            $res->set($data);     
        } 

        /*
            Middlewares
        */

        // dd($class_name, 'Controller');
        // dd($middlewares);

        foreach($middlewares as $injectable => $mids){
            $_i = explode('@', $injectable);

            $_class_name  = $_i[0];

            if (!is_array($mids)){
                $mids = [ $mids ];
            }

            foreach ($mids as $mid){
                $_method = $_i[1] ??  'index';

                if ($class_name == $_class_name && ($_method == '__all__'|| $method == $_method)){
                    // dd(['class' => $_class_name, 'method' => $_method], "MID $mid");

                    if (!class_exists($mid)){
                        $res->error(trans("Middleware '$mid' not found"), 404, "Internal error - controller class $class_name not found");                     
                    }                    

                    $mid_obj = new $mid();
                    $mid_obj->handle();
                }
            }
        }

        //////////////////////////////////////

        $res->flush();
        exit;
    }
}


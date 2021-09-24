<?php

namespace simplerest\core;

use simplerest\libs\Url;
use simplerest\libs\Arrays;
use simplerest\libs\Debug;

use simplerest\libs\Files;
use simplerest\libs\Time;
use simplerest\libs\Factory;

class FrontController
{
    const DEFAULT_ACTION = "index";

    static function resolve()
    {
        global $argv;
        global $api_version;

        $config = config();
        
        Response::getInstance();    
        $sub = (int) $config['REMOVE_API_SLUG'];

        if (php_sapi_name() != 'cli'){
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = preg_replace('/(.*)\/index.php/', '/', $path);
    
            if ($config['BASE_URL'] != '/' && strpos($path, $config['BASE_URL']) === 0) {
                $path = substr($path, strlen($config['BASE_URL']));
            }   
    
            if ($path === false || ! Url::url_check($_SERVER['REQUEST_URI']) )
                Response::getInstance()->sendError('Malformed url', 400); 
    
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

        $is_auth = ((!$config['REMOVE_API_SLUG'] && isset($_params[2]) && $_params[2] === 'auth') || ($config['REMOVE_API_SLUG'] && $_params[1] == 'auth'));

        $sub = (int) $config['REMOVE_API_SLUG'];

        if ($is_auth){
            $namespace = 'simplerest\\controllers\\';

            $class_file =  CONTROLLERS_PATH. 'MyAuthController.php';
        
            $action = $_params[3 - $sub] ?? null;  
            $params = array_slice($_params, 4 - $sub);
            $req->setParams($params);                    

            $method = !empty($action) ? $action : self::DEFAULT_ACTION;
    
            $class_name = 'MyAuth';
            $class_name = "${namespace}${class_name}Controller";

            //dd($class_name, 'CLASS_NAME:');
            //dd($method, 'METHOD:');

            $api_version = $_params[1 - $sub]; 

        } elseif ($_params[0] === 'api' || $config['REMOVE_API_SLUG']) {
            if (!isset($_params[1 - $sub]))
                Response::getInstance()->sendError('API version is missing');

            if (!preg_match('/^v[0-9]+(\.+[0-9]+)?$/', $_params[1 - $sub], $matches) )
                Response::getInstance()->sendError("Incorrect format for API version");

            $api_version = $_params[1 - $sub]; 
            $controller = $_params[2 - $sub] ?? NULL;  
                
            // CamelCase to came_case
            $controller = implode('',array_map('ucfirst',explode('_',$controller)));
        
            if ($controller == 'trash_can' || $controller == 'trashCan' || $controller == 'TrashCan' || $controller == 'collections' || $controller == 'Collections') {
                $namespace = 'simplerest\\core\\api\\'. $api_version . '\\';
            } else {
                $namespace = 'simplerest\\controllers\\api\\';
            }    

            $class_name = $namespace . ucfirst($controller); //
            
            $asked_method = NULL;
            if ($config['method_override']['by_url']){
                $asked_method  =  $req->shiftQuery('_method');
            }

            if ($asked_method == NULL && $config['method_override']['by_header']){
                $asked_method  =  $req->header('X-HTTP-Method-Override'); 
            }

            $method = $asked_method != NULL ? strtolower($asked_method) : strtolower($_SERVER['REQUEST_METHOD']);

            $params = array_slice($_params,3 - $sub,2);  // *
            

            $req->setParams($params);  

        } else {
            //dd($_params, 'PARAMS:');

            $namespace = 'simplerest\\controllers\\';

            if (empty($_params) || $_params[0]==''){
                $class_file = substr($config['DEFAULT_CONTROLLER'],0, strlen($config['DEFAULT_CONTROLLER'])-10);
                $class_name = ucfirst($class_file);
                $class_name = "${namespace}${class_name}Controller";
                $method = self::DEFAULT_ACTION;  
                $params = [];      
            }else{
                // Hipótesis
                $ix = 0;
                $folder = '';
                $controller = $_params[$ix];

                $class_file =  CONTROLLERS_PATH.ucfirst($controller).'Controller.php';
                $cnt  = count($_params) -1;
                while (!file_exists($class_file) && ($ix < $cnt)){
                    $ix++;
                    $folder = implode(DIRECTORY_SEPARATOR, array_slice($_params,0,$ix)). DIRECTORY_SEPARATOR;
                    //dd($folder, 'NAMESPACE:');
                    $controller = $_params[$ix];
                    $class_file =  CONTROLLERS_PATH. $folder. ucfirst($controller).'Controller.php';
                    //dd($class_file, "Probando ...");
                }

                //dd($class_file, "Probando ...");
                
                $action = $_params[$ix+1] ?? null;
                $params = array_slice($_params,$ix+2);
                $req->setParams($params);                    

                $method = !empty($action) ? $action : self::DEFAULT_ACTION;
        
                $class_name = ucfirst($controller);
                $class_name = "${namespace}${folder}${class_name}Controller";

                //dd($class_name, 'CLASS_NAME:');
                //dd($method, 'METHOD:');
            }
        }


        if (!class_exists($class_name))
            Response::getInstance()->sendError(_("Class not found"), 404, "Internal error - controller class $class_name not found");  

        if (!method_exists($class_name, $method)){
            if (php_sapi_name() != 'cli' || $method != self::DEFAULT_ACTION){
                Response::getInstance()->sendError("Internal error - method $method was not found in $class_name", 500); 
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
                Response::getInstance()->sendError("Not authorized for $controller:$method", 403);
            }
        }

        $data = call_user_func_array([$controller_obj, $method], $params);
        Response::getInstance()->send($data);
        
        exit;
    }
}


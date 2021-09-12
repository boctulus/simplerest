<?php

namespace simplerest\core;

use simplerest\libs\Url;
use simplerest\libs\Strings;
use simplerest\libs\Factory;
use simplerest\libs\Debug;

class Route 
{
    protected static $routes    = [];
    protected static $params;
    protected static $current   = [];
    protected static $instance;
    protected static $wheres    = [];
    protected static $ck_params = [];
    protected static $ctrls     = [];
    protected static $current_verb;
    protected static $current_uri;
    protected static $aliases   = [];
    protected static $v_aliases = [];


    protected function __construct() { }

    protected static function setup() {
        global $argv;
        
        $config = Factory::config();

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

        static::$params = $_params;
        Request::getInstance()->setParams($_params);
    }

    static function getInstance(){
        if(static::$instance == NULL){
            static::setup();
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function resolve(){
        //dd(static::$routes, 'ROUTEs');
        //dd(static::$wheres, 'WHEREs');
        //dd(static::$ck_params, 'CK PARAMS');

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = preg_replace('/(.*)\/index.php/', '/', $path);
        $path = trim($path, '/');
                
        $req_method = $_SERVER['REQUEST_METHOD'] ?? NULL;
        
        // cli 
        if ($req_method == NULL){
            // return;
        }

        if (!isset(static::$routes[$req_method])){
            return;
        }
        

        if (isset(static::$v_aliases[$req_method][$path])){
            $uri  = static::$v_aliases[$req_method][$path];

            $ck   = static::$routes[$req_method][$uri];    

            $args = static::$params;

            if (is_callable($ck)){                
                $data = $ck(...$args);
                Response::getInstance()->send($data);
            } else {
                [$class_name, $method] = static::$ctrls[$req_method][$uri];
                $controller_obj = new $class_name();

                $data = call_user_func_array([$controller_obj, $method], $args);
                Response::getInstance()->send($data);
            }

        } else {
            $callbacks = static::$routes[$req_method];

            foreach($callbacks as $uri => $ck)
            {              
                $slugs = explode('/', $uri);
                //dd($slugs, 'slugs');

                $cs = count($slugs); 
                if (count(static::$params) <  $cs){
                    continue; //
                }

                //echo "URI: $uri <p/>";
                $found = 0;
                foreach ($slugs as $k => $sl){
                    //echo "Buscando $sl .. <br/>";
                    if (!isset(static::$params[$k]) ||  static::$params[$k] != $sl){
                        //dd( isset(static::$params[$k]), " => isset in params[$k]");
                        //dd(["params[$k" => static::$params[$k], 'slug' => $sl]);
                        continue 2;
                    }

                    $found++;
                    if ($found == $cs){
                        break;
                    }
                    //dd("Encontrados $found segmentos de un total de $cs", 'found');
                }
            

                // chequear
                $args = array_slice(static::$params, $cs);

                //var_dump(count(static::$ck_params[$req_method][$uri]));
                //var_dump(count($args));


                if (isset(static::$ck_params[$req_method][$uri])){  /// *
                    if (count(static::$ck_params[$req_method][$uri]) > count($args)){
                        throw new \Exception("Expecting ". count(static::$ck_params[$req_method][$uri]). ' params but '. count($args). ' was given');
                    }

                    if (isset(static::$wheres[$req_method][$uri])){
                        $vars = static::$ck_params[$req_method][$uri];
                    
                        foreach ($vars as $ix => $var){
                            $w_vars = array_keys(static::$wheres[$req_method][$uri]);

                            if (!in_array($var, $w_vars)){
                                throw new \InvalidArgumentException("Parameter '$var' is required for $req_method on '$uri'");
                            }

                            //var_dump($args[$ix]);
                            
                            $reg = static::$wheres[$req_method][$uri][$var];
                            if (preg_match("/^($reg)$/", $args[$ix]) !== 1){                        
                                throw new \InvalidArgumentException("Parameter '$var' should match '".static::$wheres[$req_method][$uri][$var]. "' expresion. Given '{$args[$ix]}'");         
                            }

                            //dd(static::$wheres[$req_method][$uri][$var], "regex for $var. Given '{$args[$ix]}'");
                        }
                    }
                }

                
                //dd($args, 'args');
                //dd($uri, 'uri'); 
                //dd($ck, 'ck');

                if (is_callable($ck)){                
                    $data = $ck(...$args);
                    Response::getInstance()->send($data);
                } else {
                    [$class_name, $method] = static::$ctrls[$req_method][$uri];
                    $controller_obj = new $class_name();

                    $data = call_user_func_array([$controller_obj, $method], $args);
                    Response::getInstance()->send($data);
                }

                exit;
            }
            
        }

        
    }

    public static function compile()
    {   
        foreach (static::$routes as $verb => $callbacks){
            foreach($callbacks as $uri => $ck){
                // anonymus functions
                if (is_callable($ck)){
                    $r = new \ReflectionFunction($ck);
                    
                    foreach ($r->getParameters() as $p){
                        static::$ck_params[$verb][$uri][] = $p->name;                        
                    };

                    //dd(static::$ck_params[$verb][$uri], "CK PARAMS : [$verb][$uri]");
                } else {
                    //dd($ck, "\$ck");

                    $namespace = Strings::contains('\\', $ck) ? '' : 'simplerest\\controllers\\';

                    $pos = strpos($ck, '@');
                    if ($pos === false){
                        $ctrl = $ck;
                        $method = 'index';
                    } else {
                        $ctrl = substr($ck, 0, $pos);
                        $method = substr($ck, $pos+1);
                    }

                    $class_name = "${namespace}${ctrl}";
                    if (!class_exists($class_name)){
                        throw new \InvalidArgumentException("Controller class $class_name not found");  
                    }

                    if (!method_exists($class_name, $method)){
                        throw new \InvalidArgumentException("Method $method was not found in $class_name"); 
                    }
                                        
                    static::$ctrls[$verb][$uri] = [$class_name, $method];
                    //dd(static::$ctrls);                    
                }   
            }
        }
    }

    /*
     Register
    */

    public static function where($arr){
        //dd([$arr, static::$current], 'where register');
        static::$wheres[static::$current[0]][static::$current[1]] = $arr;
        return static::getInstance();
    }

    public static function get(string $uri, $callback){
        $uri = Strings::removeRTrim('/', $uri);
        static::$current_verb = 'GET';
        static::$current_uri = $uri;
        static::$current = ['GET', $uri];
        static::$routes['GET'][$uri] = $callback;
        return static::getInstance();
    }

    public static function name(string $name){
        static::$aliases[$name] = [
            'verb' => static::$current_verb,
            'uri'  => static::$current_uri
        ];

        static::$v_aliases[static::$current_verb][$name] = static::$current_uri;

        return static::getInstance();
    }

    public static function getRouteByName(string $name){
        if (php_sapi_name() == 'cli'){
            return;
        }

        return http_protocol() . '://' . $_SERVER['SERVER_NAME'] . '/' . static::$aliases[$name]['uri'];
    }

    public static function post(string $uri, $callback){
        $uri = Strings::removeRTrim('/', $uri);
        static::$current_verb = 'POST';
        static::$current_uri = $uri;
        static::$current = ['POST', $uri];
        static::$routes['POST'][$uri] = $callback;
        return static::getInstance();
    }

    public static function put(string $uri, $callback){
        $uri = Strings::removeRTrim('/', $uri);
        static::$current_verb = 'PUT';
        static::$current = ['PUT', $uri];
        static::$routes['PUT'][$uri] = $callback;
        return static::getInstance();
    }

    public static function patch(string $uri, $callback){
        $uri = Strings::removeRTrim('/', $uri);
        static::$current_verb = 'PATCH';
        static::$current_uri = $uri;
        static::$current = ['PATCH', $uri];
        static::$routes['PATCH'][$uri] = $callback;
        return static::getInstance();
    }

    public static function delete(string $uri, $callback){
        $uri = Strings::removeRTrim('/', $uri);
        static::$current_verb = 'DELETE';
        static::$current_uri = $uri;
        static::$current = ['DELETE', $uri];
        static::$routes['DELETE'][$uri] = $callback;
        return static::getInstance();
    }
    
    public static function options(string $uri, $callback){
        $uri = Strings::removeRTrim('/', $uri);
        static::$current_verb = 'OPTIONS';
        static::$current_uri = $uri;
        static::$current = ['OPTIONS', $uri];
        static::$routes['OPTIONS'][$uri] = $callback;
        return static::getInstance();
    }

}


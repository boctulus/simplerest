<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\API\v1\ApiController;
use Boctulus\Simplerest\Core\Controllers\ConsoleController;
use Boctulus\Simplerest\Core\Libs\Cli;
use Boctulus\Simplerest\Core\Libs\Msg;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Libs\VarDump;

/*
    El funcionamiento "base" es el de un FrontController para la terminal

    TO-DO

    - Que procese cli_routes.php, "similar" a como hace WebRouter con routes.php

    - Soporte para funciones anonimas.

    http://simplerest.lan/prompt_generator#chat-594
*/
class CliRouter 
{
    protected static $routes       = [];
    protected static $params;
    protected static $current      = [];
    protected static $instance;
    protected static $wheres       = [];
    protected static $ck_params    = [];
    protected static $ctrls        = [];
    protected static $current_verb;
    protected static $current_uri;
    protected static $aliases      = [];
    protected static $v_aliases    = [];    
    
    // Nuove proprietà per il supporto di parametri dinamici e gruppi
    protected static $routePatterns  = []; // [verb][uri] => regex pattern
    protected static $routeParamNames = []; // [verb][uri] => array di nomi dei parametri
    protected static $groupPrefix    = '';
    
    protected static $current_command;


    protected function __construct() { }

    public static function getInstance(){
        if (static::$instance == null){
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function resolve()
    {
        if (!is_cli()){
            return;
        }

        global $argv;
        $params = array_slice($argv, 1);
        
        // Si no se proporciona ningún comando, muestra instrucciones
        if (empty($params)) {
            dd("Uso: php com {controller} [action] [argumentos]");
            return;
        }

        /*
            Si ejecuto "php com pow 2 8" entonces $key == 'pow'
        */
        $key = $params[0];

        // dd($key, 'key');        
        // dd(static::$routes, 'routes');

        if (isset(static::$routes[$key])) {
            $params = array_slice($params, 1);

            // dd($params, 'params');
            if (is_callable(static::$routes[$key])) {                
                $cb     = static::$routes[$key];
                $result = $cb(...$params);

                response()->set($result)->flush();
            } else {
                $ck = static::$routes[$key];

                $namespace = Strings::contains('\\', $ck) ? '' : 'Boctulus\\Simplerest\\Controllers\\';
                $pos = strpos($ck, '@');

                if ($pos === false){
                    $ctrl = $ck;
                    $method = 'index';
                } else {
                    $ctrl = substr($ck, 0, $pos);
                    $method = substr($ck, $pos+1);
                }

                $class_name = "{$namespace}{$ctrl}";
                if (!class_exists($class_name)){
                    throw new \InvalidArgumentException("Controller class $class_name not found");  
                }
                if (!method_exists($class_name, $method)){
                    throw new \InvalidArgumentException("Method $method was not found in $class_name"); 
                }
                                        
                // dd([$class_name, $method]);

                // Instanciar el controlador
                $instance = new $class_name();

                // Verificar si el método es invocable
                if (!is_callable([$instance, $method])) {
                    throw new \InvalidArgumentException("Method $method in $class_name is not callable");
                }

                // Ejecutar el método con los parámetros
                $result = call_user_func_array([$instance, $method], $params);

                // Retornar o manejar la respuesta según sea necesario
                response()->set($result)->flush();
            }
        }
        
        try {
            if (!isset($result)){                
                $namespace = 'Boctulus\\Simplerest\\Controllers\\';

                // dd(static::$routes, 'routes');
                // dd($key, 'key');
                // dd($params, 'params');

                // Determina el controlador
                $controllerName = $key;
               
                $className = $namespace . ucfirst($controllerName) . 'Controller';
                
                if (!class_exists($className)) {
                    dd("Error: Controlador '$className' no encontrado.");
                    return;
                }
                
                // Instancia el controlador
                $controller = new $className();
                
                // Determina la acción
                if (isset($params[1])) {            
                    if (method_exists($controller, $params[1])) {
                        $action = $params[1];
                        $actionParams = array_slice($params, 2);
                    } else {
                        $action = 'index';
                        $actionParams = array_slice($params, 1);
                    }
                } else {
                    $action = 'index';
                    $actionParams = [];
                }

                $result = call_user_func_array([$controller, $action], $actionParams);
            }           

            if ($result !== null) {
                /*
                  Replicamos la lógica del FrontController para decidir el formato de salida.
                  - Si el controlador extiende ApiController, forzamos 'json'.
                  - Si extiende ConsoleController, usamos 'dd'.
                  - Si detecta Postman/Insomnia, 'pretty_json'.
                  - Por defecto, 'dd'.
                */
                
                // Recupera el formato de salida establecido en el controlador (si lo hay).
                $output_format = $controller->getOutputFormat();

                // Si es 'test' y estamos en el navegador, forzamos 'dd'
                if ($output_format === 'test' && Request::isBrowser()) {
                    $output_format = 'dd';
                } else {
                    $output_format = 'auto';
                }

                if ($output_format === 'auto') {
                    if ($controller instanceof ApiController) {
                        $output_format = 'json';
                    } elseif ($controller instanceof ConsoleController) {
                        $output_format = 'dd';
                    } elseif (Url::isPostmanOrInsomnia()) {
                        $output_format = 'pretty_json';
                    } else {
                        $output_format = 'dd';
                    }
                }

                // Aplica el formato
                switch ($output_format) {
                    case 'json':
                        if (!Strings::isJSON($result)){
                            $result = json_encode($result);
                        }
                        break;
                        
                    case 'pretty_json':
                        if (!Strings::isJSON($result)){
                            $result = json_encode($result, JSON_PRETTY_PRINT);
                        }
                        break;
                        
                    case 'dd':
                        if (php_sapi_name() === 'cli') {
                            // Usa el formateador con colores para CLI
                            $result = Cli::formatOutput($result, 0, true);
                        } else {
                            // Usa un formateador genérico para navegador u otros entornos
                            $result = Strings::formatOutput($result);
                        }
                        break;
                }

                // Finalmente, enviamos la respuesta (lo que llama flush() y la muestra en consola)
                response()->set($result)->flush();
            }
        } catch (\Exception $e) {
            dd("Error durante la ejecución del comando: " . $e->getMessage());
        }
    }

    /*

        Metodos sin probar de implementacion pendiente    !!!

    */

    /**
     * Registra un comando CLI.
     */
    public static function command(string $cmd, $callback) {
        $$cmd = trim($cmd);

        if (static::$groupPrefix !== '') {
            $cmd = trim(static::$groupPrefix, ':') . ':' . $cmd;
        }

        static::$current_command =$cmd;
        static::$current = ['command', $cmd];
        static::$routes[$cmd] = $callback;

        return static::getInstance();
    }

    /**
     * Método para agrupar comandos con un prefijo común.
     */
    public static function group(string $prefix, callable $callback) {
        $previousPrefix = static::$groupPrefix;
        static::$groupPrefix = trim($previousPrefix, ':') . ':' . trim($prefix, ':');
        static::$groupPrefix = trim(static::$groupPrefix, ':');
        $callback();
        static::$groupPrefix = $previousPrefix;
        return static::getInstance();
    }

    /**
     * Define restricciones para los parámetros del comando.
     */
    public static function where(array $arr) {
        static::$wheres[static::$current[0]][static::$current[1]] = $arr;
        return static::getInstance();
    }

    /**
     * Define un alias para el comando.
     */
    public static function alias(string $name) {
        static::$aliases[$name] = static::$current_command;
        return static::getInstance();
    }

    /**
     * Define un nombre descriptivo para el comando.
     */
    public static function name(string $name) {
        static::$aliases[$name] = static::$current_command;
        return static::getInstance();
    }

    /**
     * Procesa las rutas CLI definidas.
     */
    public static function compile()
    {   
        // nothing
    }

}

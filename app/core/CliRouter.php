<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\API\v1\ApiController;
use Boctulus\Simplerest\Core\Controllers\ConsoleController;
use Boctulus\Simplerest\Core\Libs\Cli;
use Boctulus\Simplerest\Core\Libs\Config;
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
            return false;
        }

        global $argv;
        $params = array_slice($argv, 1);

        // Si no se proporciona ningún comando, muestra instrucciones
        if (empty($params)) {
            dd("Uso: php com {controller} [action] [argumentos]");
            return true;
        }

        /*
            Intentar matchear comandos multi-palabra

            Ejemplos:
            - php com version              -> match "version"
            - php com zippy importer run   -> intenta "zippy:importer:run", "zippy:importer", "zippy"
            - php com pow 2 8              -> match "pow" con params [2, 8]
        */
        $matchedCommand = null;
        $matchedParams = [];

        // Intentar desde el comando más largo al más corto
        for ($i = count($params); $i > 0; $i--) {
            $testKey = implode(':', array_slice($params, 0, $i));
            if (isset(static::$routes[$testKey])) {
                $matchedCommand = $testKey;
                $matchedParams = array_slice($params, $i);
                break;
            }
        }

        if ($matchedCommand !== null) {
            $params = $matchedParams;

            // Validar parámetros usando where() si están definidos
            if (isset(static::$wheres['command'][$matchedCommand])) {
                $wheres = static::$wheres['command'][$matchedCommand];

                // Obtener nombres de parámetros de la función/método
                if (is_callable(static::$routes[$matchedCommand])) {
                    $reflection = new \ReflectionFunction(static::$routes[$matchedCommand]);
                } else {
                    $ck = static::$routes[$matchedCommand];
                    $pos = strpos($ck, '@');
                    $ctrl = substr($ck, 0, $pos);
                    $method = substr($ck, $pos+1);
                    $namespace = Strings::contains('\\', $ctrl) ? '' : namespace_url() . '\\Controllers\\';
                    $class_name = "{$namespace}{$ctrl}";
                    $reflection = new \ReflectionMethod($class_name, $method);
                }

                $parameters = $reflection->getParameters();

                // Validar cada parámetro
                foreach ($parameters as $index => $parameter) {
                    $paramName = $parameter->getName();

                    if (isset($wheres[$paramName]) && isset($params[$index])) {
                        $pattern = '/^' . $wheres[$paramName] . '$/';

                        if (!preg_match($pattern, $params[$index])) {
                            dd("Error: Parameter '$paramName' with value '{$params[$index]}' does not match pattern '{$wheres[$paramName]}'");
                            return true;
                        }
                    }
                }
            }

            // dd($params, 'params');
            if (is_callable(static::$routes[$matchedCommand])) {
                $cb     = static::$routes[$matchedCommand];
                $result = $cb(...$params);

                response()->set($result)->flush();
                return true;
            } else {
                $ck = static::$routes[$matchedCommand];

                $namespace = Strings::contains('\\', $ck) ? '' : namespace_url() . '\\Controllers\\';
                $pos = strpos($ck, '@');

                if ($pos === false){
                    $ctrl = $ck;
                    $method = 'index';
                } else {
                    $ctrl = substr($ck, 0, $pos);
                    $method = substr($ck, $pos+1);
                }

                $class_name = "{$namespace}{$ctrl}";

                // Verificar si el controlador pertenece a un package y si CliRouter está habilitado
                $packageInfo = Config::getPackageFromClass($class_name);

                if ($packageInfo !== null) {
                    // Es un controlador de package, verificar configuración específica
                    $packageConsoleRouter = Config::getPackageConfig(
                        $packageInfo['vendor'],
                        $packageInfo['package'],
                        'console_router',
                        true // Default: habilitado
                    );

                    if (!$packageConsoleRouter) {
                        // CliRouter deshabilitado para este package
                        return false;
                    }
                }

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
                return true;
            }
        }
        
        try {
            if (!isset($result)){
                // Fallback to FrontController style routing
                $namespace = namespace_url() . '\\Controllers\\';

                // dd(static::$routes, 'routes');
                // dd($params, 'params');

                // Determina el controlador
                $controllerName = $params[0] ?? null;

                if ($controllerName === null) {
                    dd("Error: No se proporcionó un controlador.");
                    return true;
                }

                $className = $namespace . ucfirst($controllerName) . 'Controller';

                if (!class_exists($className)) {
                    // No se encontró controlador, devolver false para que FrontController lo intente
                    return false;
                }
                
                // Instancia el controlador
                $controller = new $className();
                
                // Determina la acción
                if (isset($params[1])) {
                    if (method_exists($controller, $params[1])) {
                        $action = $params[1];
                        $actionParams = array_slice($params, 2);
                    } elseif (method_exists($className, '__call')) {
                        // Si hay __call(), el segundo parámetro es el método "mágico"
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
                return true;
            }
        } catch (\Exception $e) {
            dd("Error durante la ejecución del comando: " . $e->getMessage());
            return true;
        }

        return false;
    }

    /*

        Metodos sin probar de implementacion pendiente    !!!

    */

    /**
     * Registra un comando CLI.
     */
    public static function command(string $cmd, $callback) {
        $cmd = trim($cmd);

        if (static::$groupPrefix !== '') {
            $cmd = trim(static::$groupPrefix, ':') . ':' . $cmd;
        }

        static::$current_command = $cmd;
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

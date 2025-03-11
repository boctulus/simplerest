<?php

namespace simplerest\core;

use simplerest\core\api\v1\ApiController;
use simplerest\core\controllers\ConsoleController;
use simplerest\core\libs\Cli;
use simplerest\core\libs\Config;
use simplerest\core\libs\Files;
use simplerest\core\libs\Msg;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Url;
use simplerest\core\libs\VarDump;

class ConsoleRouter 
{
    protected static $instance;
    
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
            dd("Uso: php com <controller> [action] [argumentos]");
            return;
        }
        
        // Determina el controlador
        $controllerName = $params[0];
        $namespace = 'simplerest\\controllers\\';
        $className = $namespace . ucfirst($controllerName) . 'Controller';
        
        if (!class_exists($className)) {
            dd("Error: Controlador '$className' no encontrado.");
            return;
        }
        
        // Instancia el controlador
        $controller = new $className();
        
        // Determina la acción
        if (isset($params[1])) {
            // Puedes ampliar esta lista o directamente comprobar con method_exists()
            $explicitMethods = ['help', 'getPhone', 'index', 'tinyurl', 'to', 'calc', 'report', 'save', 'list'];
            
            if (in_array($params[1], $explicitMethods) || method_exists($controller, $params[1])) {
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
        
        try {
            $result = call_user_func_array([$controller, $action], $actionParams);

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
}

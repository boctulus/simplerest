<?php

use simplerest\core\WebRouter;
use simplerest\core\libs\Strings;
use simplerest\core\interfaces\ICommand;

class WebRouterCommand implements ICommand 
{
    function handle($args) {
        if (empty($args)) {
            $this->help();
            return;
        }

        $action = array_shift($args);

        switch ($action) {
            case 'list':
                $this->listRoutes($args);
                break;
            default:
                $this->help();
                break;
        }
    }

    private function listRoutes($args) {
        // Asegurarnos de que las rutas están cargadas
        $this->loadAllRoutes();

        // Obtener las rutas mediante reflexión
        $routerReflection = new \ReflectionClass(WebRouter::class);
        $routesProp = $routerReflection->getProperty('routes');
        $routesProp->setAccessible(true);
        $routes = $routesProp->getValue();

        // Verificar si se especificó un método HTTP específico para filtrar
        $verb = null;
        if (!empty($args)) {
            $verb = strtoupper($args[0]);
            if (!isset($routes[$verb]) || empty($routes[$verb])) {
                dd("There are no routes defined for the method $verb.");
                return;
            }
        }

        echo PHP_EOL;
        echo "==================================================================" . PHP_EOL;
        echo " DEFINED ROUTES " . ($verb ? "($verb)" : "(ALL)") . PHP_EOL;
        echo "==================================================================" . PHP_EOL;
        echo PHP_EOL;

        // Formatear la salida para cada método HTTP
        $totalCount = 0;
        foreach ($routes as $method => $methodRoutes) {
            if ($verb && $method != $verb) {
                continue;
            }

            if (empty($methodRoutes)) {
                continue;
            }

            echo "MÉTHOD: $method" . PHP_EOL;
            echo "-----------------------------------" . PHP_EOL;

            // Ordenar rutas alfabéticamente
            ksort($methodRoutes);

            $count = 0;
            foreach ($methodRoutes as $uri => $callback) {
                $count++;
                $totalCount++;
                $routeInfo = $this->getRouteInfo($method, $uri, $callback);
                echo $routeInfo . PHP_EOL;
            }
            
            echo "Total $method: $count" . PHP_EOL;
            echo PHP_EOL;
        }

        echo "Total routes: $totalCount" . PHP_EOL . PHP_EOL;
    }

    private function loadAllRoutes() {
        // Cargar el archivo de rutas principal
        if (file_exists(CONFIG_PATH . 'routes.php')) {
            require_once CONFIG_PATH . 'routes.php';
        }
        
        // También cargar posibles archivos de rutas adicionales
        $routeFiles = glob(CONFIG_PATH . 'routes/*.php');
        foreach ($routeFiles as $file) {
            require_once $file;
        }
        
        // Compilar las rutas para asegurarnos de que se procesen todos los controladores
        WebRouter::compile();
    }

    private function getRouteInfo($method, $uri, $callback) {
        $output = "  " . str_pad($uri, 40, " ");
        
        // Averiguar si es una closure o un controlador
        if (is_callable($callback) && !is_string($callback)) {
            $output .= " => [Closure]";
        } else {
            // Obtener controladores mediante reflexión
            $routerReflection = new \ReflectionClass(WebRouter::class);
            $ctrlsProp = $routerReflection->getProperty('ctrls');
            $ctrlsProp->setAccessible(true);
            $ctrls = $ctrlsProp->getValue();

            if (isset($ctrls[$method][$uri])) {
                $controller = $ctrls[$method][$uri][0];
                $action = $ctrls[$method][$uri][1];
                
                // Acortar el nombre del controlador para mejor visualización
                $shortController = Strings::after($controller, 'simplerest\\controllers\\');
                if ($shortController === false) {
                    $shortController = Strings::after($controller, '\\controllers\\');
                    if ($shortController === false) {
                        $shortController = $controller;
                    }
                }
                
                $output .= " => $shortController@$action";
            } else {
                $output .= " => $callback";
            }
        }

        // Verificar si tiene patrones de restricción (where)
        $routerReflection = new \ReflectionClass(WebRouter::class);
        $wheresProp = $routerReflection->getProperty('wheres');
        $wheresProp->setAccessible(true);
        $wheres = $wheresProp->getValue();

        if (isset($wheres[$method][$uri])) {
            $output .= " (restrictions: ";
            foreach ($wheres[$method][$uri] as $param => $pattern) {
                $output .= "$param=$pattern, ";
            }
            $output = rtrim($output, ", ") . ")";
        }

        return $output;
    }

    function help($name = null, ...$args) {
        $str = <<<STR
        WEBROUTER COMMAND HELP
        
        Available commands:
        
        php com web_router list                  List all routes
        php com web_router list <method>         List routes for a specific HTTP method (get, post, etc.)
        
        Examples:
        php com web_router list           # List all routes
        php com web_router list get       # List only GET routes
        php com web_router list post      # List only POST routes
        STR;

        echo $str . PHP_EOL;
    }
}
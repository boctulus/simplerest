<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\WebRouter;
use Boctulus\Simplerest\Core\Libs\Strings;

class RouterListCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'list';
        $this->description = 'Lista todas las rutas registradas (o filtra por método HTTP)';
        $this->aliases     = ['ls', 'routes'];
        $this->examples    = [
            'php com router list',
            'php com router list --method=GET',
            'php com router list --method=POST',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['method'],
            'flags'    => [],
            'options'  => [
                'method' => ['describe' => 'Filtrar por método HTTP (GET, POST, PUT, DELETE, etc.)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $filterVerb = $this->opt($parsed, 'method');
        if ($filterVerb) {
            $filterVerb = strtoupper($filterVerb);
        }

        // También aceptar método como primer arg posicional
        if (!$filterVerb && !empty($parsed['_positional'][0])) {
            $filterVerb = strtoupper($parsed['_positional'][0]);
        }

        $this->loadAllRoutes();

        $routerReflection = new \ReflectionClass(WebRouter::class);

        $routesProp = $routerReflection->getProperty('routes');
        $routesProp->setAccessible(true);
        $routes = $routesProp->getValue();

        if ($filterVerb && (!isset($routes[$filterVerb]) || empty($routes[$filterVerb]))) {
            echo "No hay rutas definidas para el método {$filterVerb}.\n";
            return;
        }

        echo PHP_EOL;
        echo str_repeat('=', 66) . PHP_EOL;
        echo ' RUTAS DEFINIDAS' . ($filterVerb ? " ({$filterVerb})" : ' (TODAS)') . PHP_EOL;
        echo str_repeat('=', 66) . PHP_EOL . PHP_EOL;

        $total = 0;
        foreach ($routes as $method => $methodRoutes) {
            if ($filterVerb && $method !== $filterVerb) continue;
            if (empty($methodRoutes)) continue;

            echo "MÉTODO: {$method}\n";
            echo str_repeat('-', 40) . "\n";

            ksort($methodRoutes);
            $count = 0;
            foreach ($methodRoutes as $uri => $callback) {
                $count++;
                $total++;
                echo $this->getRouteInfo($method, $uri, $callback) . PHP_EOL;
            }
            echo "Total {$method}: {$count}\n\n";
        }

        echo "Total de rutas: {$total}\n\n";
    }

    private function loadAllRoutes(): void
    {
        if (file_exists(CONFIG_PATH . 'routes.php')) {
            require_once CONFIG_PATH . 'routes.php';
        }
        foreach (glob(CONFIG_PATH . 'routes/*.php') ?: [] as $file) {
            require_once $file;
        }
        WebRouter::compile();
    }

    private function getRouteInfo(string $method, string $uri, mixed $callback): string
    {
        $output = '  ' . str_pad($uri, 40, ' ');

        if (is_callable($callback) && !is_string($callback)) {
            $output .= ' => [Closure]';
        } else {
            $reflection = new \ReflectionClass(WebRouter::class);
            $ctrlsProp  = $reflection->getProperty('ctrls');
            $ctrlsProp->setAccessible(true);
            $ctrls = $ctrlsProp->getValue();

            if (isset($ctrls[$method][$uri])) {
                $controller = $ctrls[$method][$uri][0];
                $action     = $ctrls[$method][$uri][1];
                $short      = Strings::after($controller, 'Boctulus\\Simplerest\\Controllers\\')
                           ?: Strings::after($controller, '\\controllers\\')
                           ?: $controller;
                $output .= " => {$short}@{$action}";
            } else {
                $output .= " => {$callback}";
            }
        }

        $reflection = new \ReflectionClass(WebRouter::class);
        $wheresProp = $reflection->getProperty('wheres');
        $wheresProp->setAccessible(true);
        $wheres = $wheresProp->getValue();

        if (isset($wheres[$method][$uri])) {
            $parts = [];
            foreach ($wheres[$method][$uri] as $param => $pattern) {
                $parts[] = "{$param}={$pattern}";
            }
            $output .= ' (restrictions: ' . implode(', ', $parts) . ')';
        }

        return $output;
    }
}

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
            'php com router list --from-config',
            'php com router list -c',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['method'],
            'flags'    => ['from_config', 'c'],
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

        $fromConfig = $this->opt($parsed, 'from_config', false) || $this->opt($parsed, 'c', false);
        if ($fromConfig) {
            $this->showRoutesFromConfig($filterVerb);
            return;
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

    // ─── Modo --from-config ────────────────────────────────────────

    private function showRoutesFromConfig(?string $filterVerb): void
    {
        $routes = [];

        foreach (glob(CONFIG_PATH . 'routes/*.php') ?: [] as $file) {
            $routes = array_merge($routes, $this->parseConfigFile($file, 'routes/*.php'));
        }
        if (file_exists(CONFIG_PATH . 'routes.php')) {
            $routes = array_merge($routes, $this->parseConfigFile(CONFIG_PATH . 'routes.php', 'routes.php'));
        }

        $apiRoutes = $this->scanApiControllers();
        $routes = array_merge($routes, $apiRoutes);

        if (empty($routes)) {
            echo "\nNo se encontraron rutas.\n\n";
            return;
        }

        $grouped = [];
        foreach ($routes as $r) {
            $grouped[$r['method']][] = $r;
        }
        ksort($grouped);

        echo PHP_EOL;
        echo str_repeat('=', 72) . PHP_EOL;
        echo ' RUTAS (from config + Api/*)' . ($filterVerb ? " [{$filterVerb}]" : '') . PHP_EOL;
        echo str_repeat('=', 72) . PHP_EOL . PHP_EOL;

        $total = 0;
        foreach ($grouped as $method => $methodRoutes) {
            if ($filterVerb && $method !== $filterVerb) continue;
            if (empty($methodRoutes)) continue;

            echo "MÉTODO: {$method}\n";
            echo str_repeat('-', 44) . "\n";

            usort($methodRoutes, fn($a, $b) => strcmp($a['uri'], $b['uri']));

            $count = 0;
            foreach ($methodRoutes as $r) {
                $count++;
                $total++;
                $line = '  ' . str_pad($r['uri'], 44, ' ');
                $line .= ' => ' . $r['handler'];
                if (!empty($r['source'])) {
                    $line .= '  [' . $r['source'] . ']';
                }
                echo $line . PHP_EOL;
            }
            echo "Total {$method}: {$count}\n\n";
        }

        echo "Total de rutas: {$total}\n\n";
    }

    private function parseConfigFile(string $filePath, string $label): array
    {
        $source = file_get_contents($filePath);
        $tokens = @token_get_all($source);

        $routes = [];
        $n = count($tokens);

        for ($i = 0; $i < $n; $i++) {
            if (!is_array($tokens[$i])) continue;

            $isWebRouter = ($tokens[$i][0] === T_STRING && $tokens[$i][1] === 'WebRouter');
            $isVariable  = ($tokens[$i][0] === T_VARIABLE);

            if (!$isWebRouter && !$isVariable) continue;

            $j = $i + 1;
            while ($j < $n && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) $j++;
            if ($j >= $n || !is_array($tokens[$j])) continue;

            $isStatic   = ($isWebRouter && $tokens[$j][0] === T_DOUBLE_COLON);
            $isDynamic  = ($isVariable && $tokens[$j][0] === T_OBJECT_OPERATOR);
            if (!$isStatic && !$isDynamic) continue;

            $k = $j + 1;
            while ($k < $n && is_array($tokens[$k]) && $tokens[$k][0] === T_WHITESPACE) $k++;
            if ($k >= $n || !is_array($tokens[$k]) || $tokens[$k][0] !== T_STRING) continue;

            $methodName = strtoupper($tokens[$k][1]);
            if (!in_array($methodName, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'ANY', 'MATCH', 'GROUP'])) continue;

            $l = $k + 1;
            while ($l < $n && is_array($tokens[$l]) && $tokens[$l][0] === T_WHITESPACE) $l++;
            if ($l >= $n || $tokens[$l] !== '(') continue;

            if ($methodName === 'GROUP') {
                continue;
            }

            $m = $l + 1;
            while ($m < $n && is_array($tokens[$m]) && $tokens[$m][0] === T_WHITESPACE) $m++;
            if ($m >= $n || !is_array($tokens[$m]) || $tokens[$m][0] !== T_CONSTANT_ENCAPSED_STRING) continue;

            $uri = trim($tokens[$m][1], '\'"');

            $p = $m + 1;
            while ($p < $n && is_array($tokens[$p]) && $tokens[$p][0] === T_WHITESPACE) $p++;
            if ($p < $n && $tokens[$p] === ',') $p++;
            while ($p < $n && is_array($tokens[$p]) && $tokens[$p][0] === T_WHITESPACE) $p++;

            $handler = '[Closure]';
            if ($p < $n) {
                if (is_array($tokens[$p]) && $tokens[$p][0] === T_CONSTANT_ENCAPSED_STRING) {
                    $handler = trim($tokens[$p][1], '\'"');
                } elseif ($tokens[$p][0] === T_STRING && $tokens[$p][1] === 'function') {
                    $hint = $this->extractClosureHint($tokens, $p, $n);
                    $handler = $hint ?: '[Closure]';
                }
            }

            $verbs = ($methodName === 'ANY')
                ? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']
                : [$methodName];

            foreach ($verbs as $v) {
                $routes[] = [
                    'method'  => $v,
                    'uri'     => $uri,
                    'handler' => $handler,
                    'source'  => $label,
                ];
            }
        }

        return $routes;
    }

    private function extractClosureHint(array $tokens, int $start, int $n): string
    {
        $depth = 0;
        $captured = [];
        $capturing = false;

        for ($i = $start; $i < $n; $i++) {
            if (is_array($tokens[$i])) {
                $tokenVal = $tokens[$i][1];
            } else {
                $ch = $tokens[$i];
                if ($ch === '(' || $ch === ')' || $ch === '{' || $ch === '}') {
                    $tokenVal = $ch;
                } else {
                    $tokenVal = $ch;
                }
            }

            $captured[] = $tokenVal;

            if ($tokenVal === '{') {
                if (!$capturing) {
                    $capturing = true;
                    $captured = [];
                }
                $depth++;
            } elseif ($tokenVal === '}') {
                $depth--;
                if ($capturing && $depth === 0) {
                    break;
                }
            } elseif ($tokenVal === '(' && !$capturing) {
                $captured = [];
            }
        }

        $body = trim(implode('', $captured));
        $body = preg_replace('/\s+/', ' ', $body);
        $firstLine = trim(explode("\n", $body)[0] ?? '');
        $firstLine = mb_strimwidth($firstLine, 0, 60, '…');

        return $firstLine !== '' ? "[Closure] {$firstLine}" : '[Closure]';
    }

    private function scanApiControllers(): array
    {
        $routes = [];
        $apiDir = APP_PATH . 'Controllers' . DIRECTORY_SEPARATOR . 'Api';

        if (!is_dir($apiDir)) {
            return $routes;
        }

        $files = glob($apiDir . DIRECTORY_SEPARATOR . '*.php');
        sort($files);

        $standardHttp = ['get' => 'GET', 'post' => 'POST', 'put' => 'PUT', 'patch' => 'PATCH', 'delete' => 'DELETE'];

        foreach ($files as $file) {
            $ctrlName  = pathinfo($file, PATHINFO_FILENAME);
            $uriPart   = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $ctrlName));
            $uriBase   = 'api/v1/' . $uriPart;

            $hasBaseApi = $this->classExtendsApiController($file);
            if (!$hasBaseApi) {
                $methods = $this->getPublicMethods($file);
                foreach ($methods as $m) {
                    $routes[] = [
                        'method'  => 'GET',
                        'uri'     => $uriBase . '/' . lcfirst($m),
                        'handler' => $ctrlName . '::' . $m . '()',
                        'source'  => 'Api/',
                    ];
                }
                continue;
            }

            foreach ($standardHttp as $method => $httpVerb) {
                $routes[] = [
                    'method'  => $httpVerb,
                    'uri'     => $uriBase,
                    'handler' => $ctrlName . '::' . $method . '()',
                    'source'  => 'Api/',
                ];
            }

            $routes[] = [
                'method'  => 'GET',
                'uri'     => $uriBase . '/{id}',
                'handler' => $ctrlName . '::get($id)',
                'source'  => 'Api/',
            ];

            $customMethods = $this->getCustomApiMethods($file, $ctrlName);
            foreach ($customMethods as $cm) {
                $routes[] = [
                    'method'  => 'GET',
                    'uri'     => $uriBase . '/' . lcfirst($cm),
                    'handler' => $ctrlName . '::' . $cm . '()',
                    'source'  => 'Api/',
                ];
            }
        }

        return $routes;
    }

    private function classExtendsApiController(string $filePath): bool
    {
        $source = file_get_contents($filePath);
        if (preg_match('/\bextends\s+ApiController\b/', $source)) {
            return true;
        }
        if (preg_match('/\bextends\s+ResourceController\b/', $source)) {
            return true;
        }
        return false;
    }

    private function getPublicMethods(string $filePath): array
    {
        $source = file_get_contents($filePath);
        preg_match_all('/function\s+(\w+)\s*\(/', $source, $m);
        $all = $m[1];

        return array_values(array_filter($all, fn($f) => $f !== '__construct'));
    }

    private function getCustomApiMethods(string $filePath, string $ctrlName): array
    {
        $source = file_get_contents($filePath);
        preg_match_all('/function\s+(\w+)\s*\(/', $source, $m);
        $all = $m[1];

        $skip = ['__construct', 'get', 'post', 'put', 'patch', 'delete', 'head', 'options'];

        return array_values(array_filter($all, fn($f) => !in_array($f, $skip)));
    }
}

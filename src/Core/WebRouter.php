<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Msg;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Url;

/*
    @author Pablo Bozzolo

    TO-DO

    Manejar mejor los casos en los que la ruta no coincide con el patrón definido en where(), devolviendo un error más claro antes de que el framework intente cargar un controlador inexistente.

    Implementar un manejador de excepciones que capture el error CLASS_NOT_FOUND y devuelva una respuesta más clara (ej. "Ruta no encontrada" en lugar de "Internal error").
*/

class WebRouter
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

    protected function __construct() { }

    protected static function setup() {
        global $argv;
        
        $config = Config::get();

        if (php_sapi_name() != 'cli'){
            $res = response();
    
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = preg_replace('/(.*)\/index.php/', '/', $path);

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
        } else {
            $_params = array_slice($argv, 1);
        }

        static::$params = $_params;
        Request::getInstance()->setParams($_params);
    }

    public static function getInstance(){
        if(static::$instance == NULL){
            static::setup();
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Metodo per raggruppare rotte con un prefisso comune.
     * Esempio:
     *   WebRouter::group('admin', function() {
     *       WebRouter::get('dashboard', 'AdminController@dashboard');
     *   });
     */
    public static function group(string $prefix, callable $callback) {
        $previousPrefix = static::$groupPrefix;
        static::$groupPrefix = trim($previousPrefix, '/') . '/' . trim($prefix, '/');
        static::$groupPrefix = trim(static::$groupPrefix, '/');
        $callback();
        static::$groupPrefix = $previousPrefix;
        return static::getInstance();
    }

    public static function resolve()
    {
        if (is_cli()){
            return;
        }

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = preg_replace('/(.*)\/index.php/', '/', $path);
        $path = trim($path, '/');
                
        $req_method = $_SERVER['REQUEST_METHOD'] ?? NULL;
        
        if ($req_method == NULL){
            // CLI o metodo non definito
        }

        if (!isset(static::$routes[$req_method])){
            return;
        }

        $res = Response::getInstance(); 
        
        // Verifica alias di URL esatto
        if (isset(static::$v_aliases[$req_method][$path])){
            $uri  = static::$v_aliases[$req_method][$path];
            $ck   = static::$routes[$req_method][$uri];    
            $args = static::$params;

            if (is_callable($ck)){
                // Se la rotta es una closure, la ejecuto directamente.
                $data = $ck(...$args);
                $res->set($data);
            } else {
                // Rotta che richiama un controller.
                [$class_name, $method] = static::$ctrls[$req_method][$uri];

                // Verificar si el controlador pertenece a un package y si WebRouter está habilitado
                $packageInfo = Config::getPackageFromClass($class_name);

                if ($packageInfo !== null) {
                    // Es un controlador de package, verificar configuración específica
                    $packageWebRouter = Config::getPackageConfig(
                        $packageInfo['vendor'],
                        $packageInfo['package'],
                        'web_router',
                        true // Default: habilitado
                    );

                    if (!$packageWebRouter) {
                        // WebRouter deshabilitado para este package
                        return;
                    }
                }

                $controller_obj = new $class_name();
                $data = call_user_func_array([$controller_obj, $method], $args);

                $res->set($data);
            }

            if (!headers_sent()){
                $res->flush();
            }
            exit;

        } else {
            $callbacks = static::$routes[$req_method];

            foreach($callbacks as $uri => $ck)
            {              
                $args = [];
                // Se la rotta è dinamica (contiene parametri nominati)
                if (isset(static::$routePatterns[$req_method][$uri])) {
                    $pattern = static::$routePatterns[$req_method][$uri];
                    if (preg_match($pattern, $path, $matches)) {
                        array_shift($matches); // rimuove il match completo

                        // Gestione del parametro wildcard: se presente, deve essere l'ultimo elemento
                        $args = $matches;

                        // Se la route ha un wildcard, assicurarsi che il valore wildcard sia l'ultimo argomento
                        if (isset(static::$routeParamNames[$req_method][$uri]) &&
                            in_array('wildcard', static::$routeParamNames[$req_method][$uri])) {

                            // Trova l'indice del parametro wildcard
                            $wildcardIndex = array_search('wildcard', static::$routeParamNames[$req_method][$uri]);

                            // Se il valore del wildcard è vuoto ma abbiamo altri segmenti, potrebbe essere un problema
                            if (isset($args[$wildcardIndex]) && $args[$wildcardIndex] === '') {
                                // Cerchiamo di ottenere il resto del path come wildcard
                                // Rimuoviamo il suffisso '/*' dall'URI originale per confrontarlo con il path
                                $cleanUri = rtrim($uri, '/*');
                                $uriParts = explode('/', trim($cleanUri, '/'));
                                $pathParts = explode('/', trim($path, '/'));

                                // Calcoliamo quanti segmenti ci sono dopo i segmenti fissi
                                $fixedSegmentsCount = count($uriParts);

                                if (count($pathParts) >= $fixedSegmentsCount) {
                                    $wildcardValue = implode('/', array_slice($pathParts, $fixedSegmentsCount));
                                    $args[$wildcardIndex] = $wildcardValue;
                                }
                            }
                        }
                    } else {
                        continue;
                    }
                } else {
                    // Matching letterale per rotte statiche (esistente logica)
                    $slugs = explode('/', $uri);
                    if (count(static::$params) < count($slugs)){
                        continue;
                    }

                    $found = 0;
                    foreach ($slugs as $k => $sl){
                        if (!isset(static::$params[$k]) || static::$params[$k] != $sl){
                            continue 2;
                        }
                        $found++;
                        if ($found == count($slugs)){
                            break;
                        }
                    }
                    $args = array_slice(static::$params, count($slugs));

                    if (isset(static::$ck_params[$req_method][$uri])){
                        if (count(static::$ck_params[$req_method][$uri]) > count($args)){
                            throw new \Exception("Expecting " . count(static::$ck_params[$req_method][$uri]) . ' params but ' . count($args) . ' was given');
                        }

                        if (isset(static::$wheres[$req_method][$uri])){
                            $vars = static::$ck_params[$req_method][$uri];
                            foreach ($vars as $ix => $var){
                                $w_vars = array_keys(static::$wheres[$req_method][$uri]);
                                if (!in_array($var, $w_vars)){
                                    throw new \InvalidArgumentException("Parameter '$var' is required for $req_method on '$uri'");
                                }
                                $reg = static::$wheres[$req_method][$uri][$var];
                                if (preg_match("/^($reg)$/", $args[$ix]) !== 1){
                                    throw new \InvalidArgumentException("Parameter '$var' should match '" . static::$wheres[$req_method][$uri][$var] . "' expression. Given '{$args[$ix]}'");
                                }
                            }
                        }
                    }
                }

                if (is_callable($ck)){
                    $data = $ck(...$args);
                    $res->set($data);
                } else {
                    [ $class_name, $method ] = static::$ctrls[$req_method][$uri];

                    // Verificar si el controlador pertenece a un package y si WebRouter está habilitado
                    $packageInfo = Config::getPackageFromClass($class_name);

                    if ($packageInfo !== null) {
                        // Es un controlador de package, verificar configuración específica
                        $packageWebRouter = Config::getPackageConfig(
                            $packageInfo['vendor'],
                            $packageInfo['package'],
                            'web_router',
                            true // Default: habilitado
                        );

                        if (!$packageWebRouter) {
                            // WebRouter deshabilitado para este package, continuar buscando otras rutas
                            continue;
                        }
                    }

                    $controller_obj = new $class_name();

                    // dd(get_class($controller_obj));
                    // dd($method);
                    // dd($args);

                    $data = call_user_func_array([$controller_obj, $method], $args);                    
                    $data = $data ?? response()->get();
                    
                    // Inizio blocco middleware.
                    $middlewares = include CONFIG_PATH . 'middlewares.php';
                    
                    // dd($middlewares, 'MIDS');
                    // dd($class_name, 'CONTROLLER');
                    // dd($method, 'ACTION');

                    $res->set($data);

                    /*
                        Middlewares
                    */

                    foreach($middlewares as $injectable => $mids){
                        $_i = explode('@', $injectable);
                        $_class_name = $_i[0];
                        if (!is_array($mids)){
                            $mids = [ $mids ];
                        }
                        foreach ($mids as $mid){
                            $_method = $_i[1] ?? 'index';
                            if ($class_name == $_class_name && ($_method == '__all__' || $method == $_method)){
                                if (!class_exists($mid)){
                                    $res->error("Middleware '$mid' not found", 404, "Internal error - controller class $class_name not found");
                                }
                                $mid_obj = new $mid();
                                $mid_obj->handle();
                            }
                        }
                    }
                    // Fine blocco middleware.
                }          

                if (!headers_sent()){
                    $res->flush();
                }
                exit;
            }
        }
    }

    public static function mount(string $prefix, string $handlerClass)
    {
        static::any(trim($prefix, '/') . '/*', function(...$args) use ($handlerClass) {
            $handler = new $handlerClass();
            return $handler->resolve($args);
        });

        return static::getInstance();
    }

    public static function compile()
    {
        foreach (static::$routes as $verb => $callbacks){
            foreach($callbacks as $uri => $ck){
                // Per funzioni anonime
                if (is_callable($ck)){
                    $r = new \ReflectionFunction($ck);
                    foreach ($r->getParameters() as $p){
                        static::$ck_params[$verb][$uri][] = $p->name;
                    }
                } elseif (is_string($ck)) {
                    $namespace = Strings::contains('\\', $ck) ? '' : namespace_url(true) . 'Controllers\\';
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

                    static::$ctrls[$verb][$uri] = [$class_name, $method];
                }

                // Se la rotta contiene parametri dinamici (placeholder {param}) o wildcard
                if (strpos($uri, '{') !== false || str_ends_with($uri, '/*')) {
                    $hasWildcard = str_ends_with($uri, '/*');
                    $baseUri = $hasWildcard ? substr($uri, 0, -2) : $uri;

                    $pattern = preg_replace_callback('/\{(\w+)\}/', function($matches) use ($verb, $uri) {
                        $param = $matches[1];
                        if (isset(static::$wheres[$verb][$uri][$param])) {
                            return '(' . static::$wheres[$verb][$uri][$param] . ')';
                        }
                        return '([^/]+)';
                    }, $baseUri);

                    if ($hasWildcard) {
                        $pattern .= '(?:/(.*))?'; // Captura todo lo que sigue después de /
                    }

                    static::$routePatterns[$verb][$uri] = '#^' . $pattern . '$#';

                    // Estrai i nomi dei parametri
                    preg_match_all('/\{(\w+)\}/', $uri, $paramMatches);
                    $paramNames = $paramMatches[1];

                    if ($hasWildcard) {
                        $paramNames[] = 'wildcard'; // Aggiungi il nome del parametro wildcard
                    }

                    static::$routeParamNames[$verb][$uri] = $paramNames;
                }
            }

            // --- ORDENAR rutas del verbo $verb por especificidad ---
            // Calculamos una métrica: más literales => más específico; más segmentos => más específico; menos params => más específico
            $uris = array_keys(static::$routes[$verb]);

            usort($uris, function($a, $b) use ($verb) {
                $calc = function($uri) {
                    $hasWildcard = str_ends_with($uri, '/*');
                    $baseUri = $hasWildcard ? substr($uri, 0, -2) : $uri;

                    $segments = array_values(array_filter(explode('/', $baseUri), 'strlen'));
                    $numSegments = count($segments);
                    $numParams = preg_match_all('/\{(\w+)\}/', $uri, $pm);
                    $numLiterals = 0;
                    foreach ($segments as $s) {
                        if (strpos($s, '{') === false && $s !== '') $numLiterals++;
                    }

                    // Las rutas con wildcard tienen menor prioridad
                    $wildcardFactor = $hasWildcard ? 0 : 1;

                    // Devolvemos una tupla para comparar: (wildcardFactor, numLiterals, numSegments, -numParams)
                    return [$wildcardFactor, $numLiterals, $numSegments, -$numParams];
                };

                $va = $calc($a);
                $vb = $calc($b);

                // Comparación lexicográfica: preferir rutas sin wildcard, luego más literales, luego más segmentos, luego menos params
                if ($va[0] !== $vb[0]) return ($va[0] > $vb[0]) ? -1 : 1; // Sin wildcard primero
                if ($va[1] !== $vb[1]) return ($va[1] > $vb[1]) ? -1 : 1; // Más literales primero
                if ($va[2] !== $vb[2]) return ($va[2] > $vb[2]) ? -1 : 1; // Más segmentos primero
                if ($va[3] !== $vb[3]) return ($va[3] > $vb[3]) ? -1 : 1; // Menos params primero
                // Si empatan, mantener orden lexicográfico inverso por longitud (más largo -> antes)
                if (strlen($a) !== strlen($b)) return (strlen($a) > strlen($b)) ? -1 : 1;
                return strcmp($a, $b);
            });

            // Reconstruir las estructuras en el orden especificado por $uris
            $newRoutes = [];
            $newCtrls = [];
            $newPatterns = [];
            $newParamNames = [];
            $newCkParams = [];
            $newWheres = [];

            foreach ($uris as $u) {
                // routes
                $newRoutes[$u] = static::$routes[$verb][$u];

                // ctrls (si existen)
                if (isset(static::$ctrls[$verb][$u])) {
                    $newCtrls[$u] = static::$ctrls[$verb][$u];
                }

                // routePatterns y paramNames
                if (isset(static::$routePatterns[$verb][$u])) {
                    $newPatterns[$u] = static::$routePatterns[$verb][$u];
                }
                if (isset(static::$routeParamNames[$verb][$u])) {
                    $newParamNames[$u] = static::$routeParamNames[$verb][$u];
                }

                // ck_params
                if (isset(static::$ck_params[$verb][$u])) {
                    $newCkParams[$u] = static::$ck_params[$verb][$u];
                }

                // wheres
                if (isset(static::$wheres[$verb][$u])) {
                    $newWheres[$u] = static::$wheres[$verb][$u];
                }
            }

            static::$routes[$verb] = $newRoutes;
            static::$ctrls[$verb] = $newCtrls;
            static::$routePatterns[$verb] = $newPatterns;
            static::$routeParamNames[$verb] = $newParamNames;
            static::$ck_params[$verb] = $newCkParams;
            static::$wheres[$verb] = $newWheres;
            // --- fin ordenamiento ---
        }
    }

    /*
     * Register where
     */
    public static function where($arr){
        static::$wheres[static::$current[0]][static::$current[1]] = $arr;
        return static::getInstance();
    }

    // Modifica nei metodi di registrazione per gestire il prefisso di gruppo
    public static function get(string $uri, $callback){
        $uri = Strings::rTrim('/', $uri);
        if(static::$groupPrefix !== ''){
            $uri = trim(static::$groupPrefix, '/') . '/' . ltrim($uri, '/');
        }
        static::$current_verb = 'GET';
        static::$current_uri = $uri;
        static::$current = ['GET', $uri];
        static::$routes['GET'][$uri] = $callback;
        return static::getInstance();
    }

    public static function post(string $uri, $callback){
        $uri = Strings::rTrim('/', $uri);
        if(static::$groupPrefix !== ''){
            $uri = trim(static::$groupPrefix, '/') . '/' . ltrim($uri, '/');
        }
        static::$current_verb = 'POST';
        static::$current_uri = $uri;
        static::$current = ['POST', $uri];
        static::$routes['POST'][$uri] = $callback;
        return static::getInstance();
    }

    public static function put(string $uri, $callback){
        $uri = Strings::rTrim('/', $uri);
        if(static::$groupPrefix !== ''){
            $uri = trim(static::$groupPrefix, '/') . '/' . ltrim($uri, '/');
        }
        static::$current_verb = 'PUT';
        static::$current = ['PUT', $uri];
        static::$routes['PUT'][$uri] = $callback;
        return static::getInstance();
    }

    public static function patch(string $uri, $callback){
        $uri = Strings::rTrim('/', $uri);
        if(static::$groupPrefix !== ''){
            $uri = trim(static::$groupPrefix, '/') . '/' . ltrim($uri, '/');
        }
        static::$current_verb = 'PATCH';
        static::$current_uri = $uri;
        static::$current = ['PATCH', $uri];
        static::$routes['PATCH'][$uri] = $callback;
        return static::getInstance();
    }

    public static function delete(string $uri, $callback){
        $uri = Strings::rTrim('/', $uri);
        if(static::$groupPrefix !== ''){
            $uri = trim(static::$groupPrefix, '/') . '/' . ltrim($uri, '/');
        }
        static::$current_verb = 'DELETE';
        static::$current_uri = $uri;
        static::$current = ['DELETE', $uri];
        static::$routes['DELETE'][$uri] = $callback;
        return static::getInstance();
    }
    
    public static function options(string $uri, $callback){
        $uri = Strings::rTrim('/', $uri);
        if(static::$groupPrefix !== ''){
            $uri = trim(static::$groupPrefix, '/') . '/' . ltrim($uri, '/');
        }
        static::$current_verb = 'OPTIONS';
        static::$current_uri = $uri;
        static::$current = ['OPTIONS', $uri];
        static::$routes['OPTIONS'][$uri] = $callback;
        return static::getInstance();
    }

    public static function any(string $uri, $callback){
        $uri = Strings::rTrim('/', $uri);

        if(static::$groupPrefix !== ''){
            $uri = trim(static::$groupPrefix, '/') . '/' . ltrim($uri, '/');
        }

        $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

        foreach ($verbs as $verb){
            static::$current_verb = $verb;
            static::$current_uri  = $uri;
            static::$current      = [$verb, $uri];
            static::$routes[$verb][$uri] = $callback;
        }

        return static::getInstance();
    }

    public static function match(array $verbs, string $uri, $callback){
        $uri = Strings::rTrim('/', $uri);

        if(static::$groupPrefix !== ''){
            $uri = trim(static::$groupPrefix, '/') . '/' . ltrim($uri, '/');
        }

        $verbs = array_map('strtoupper', $verbs);

        foreach ($verbs as $verb){
            static::$current_verb = $verb;
            static::$current_uri  = $uri;
            static::$current      = [$verb, $uri];
            static::$routes[$verb][$uri] = $callback;
        }

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
        return httpProtocol() . '://' . $_SERVER['SERVER_NAME'] . '/' . static::$aliases[$name]['uri'];
    }

    public static function fromArray(array $routes) {
        // Definizione dei verbi HTTP supportati
        $supportedVerbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

        foreach ($routes as $routeKey => $callback) {
            // Se il formato della chiave contiene ":", significa che è specificato un verbo
            if (strpos($routeKey, ':') !== false) {
                list($verb, $uri) = explode(':', $routeKey, 2);
                $verb = strtoupper(trim($verb));
                $uri  = trim($uri, '/');

                // Se il verbo è supportato, chiama il metodo di registrazione corrispondente
                if (in_array($verb, $supportedVerbs)) {
                    switch ($verb) {
                        case 'GET':
                            self::get($uri, $callback);
                            break;
                        case 'POST':
                            self::post($uri, $callback);
                            break;
                        case 'PUT':
                            self::put($uri, $callback);
                            break;
                        case 'PATCH':
                            self::patch($uri, $callback);
                            break;
                        case 'DELETE':
                            self::delete($uri, $callback);
                            break;
                        case 'OPTIONS':
                            self::options($uri, $callback);
                            break;
                    }
                } else {
                    // Se il verbo specificato non è riconosciuto, registra per tutti i verbi supportati
                    foreach ($supportedVerbs as $v) {
                        call_user_func([__CLASS__, strtolower($v)], $uri, $callback);
                    }
                }
            } else {
                // Se non viene specificato alcun verbo, registra la rotta per tutti i verbi supportati
                $uri = trim($routeKey, '/');
                foreach ($supportedVerbs as $v) {
                    call_user_func([__CLASS__, strtolower($v)], $uri, $callback);
                }
            }
        }
        return static::getInstance();
    }

    // Public methods to access internal route data for testing purposes
    public static function getRoutes() {
        return static::$routes;
    }

    public static function getRoutePatterns() {
        return static::$routePatterns;
    }

    public static function getRouteParamNames() {
        return static::$routeParamNames;
    }
    
}

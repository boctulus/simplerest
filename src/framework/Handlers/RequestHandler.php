<?php

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Libs\Msg;
use Boctulus\Simplerest\Core\Response;

class RequestHandler
{
    /**
     * Parse request and determine environment
     *
     * @param string $env 'cli' or 'http'
     * @return array [$params, $is_auth, $is_api]
     */
    public function parse(string $env): array
    {
        $config = Config::get();
        $res = Response::getInstance();

        if ($env === 'http') {
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
                    exit;
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
        } else {
            global $argv;
            $_params = array_slice($argv, 1);
        }

        if (!isset($_params[0])){
            return [[], false, false]; // No params, not auth, not api
        }

        $sub = (int) $config['remove_api_slug'];

        $is_auth = ((!$config['remove_api_slug'] && isset($_params[2]) && $_params[2] === 'auth') ||
                    ($config['remove_api_slug'] && isset($_params[1]) && $_params[1] == 'auth'));

        $is_api = ($_params[0] === 'api' || $config['remove_api_slug']);

        return [$_params, $is_auth, $is_api];
    }

    /**
     * Resolve controller, method and arguments for regular routes
     *
     * @param array $params
     * @return array [$className, $methodName, $arguments]
     */
    public function resolveController(array $params): array
    {
        $config = Config::get();
        $namespace = namespace_url() . '\\Controllers\\';

        if (empty($params) || $params[0]==''){
            $class_file = substr($config['default_controller'],0, strlen($config['default_controller'])-10);
            $class_name = Strings::snakeToCamel($class_file);
            $class_name = "{$namespace}{$class_name}Controller";
            $method = 'index';
            $args = [];
        } else {
            // Hipótesis: el controller puede estar en subcarpetas
            $ix = 0;
            $folder = '';
            $controller = $params[$ix];

            $class_file =  CONTROLLERS_PATH.Strings::snakeToCamel($controller).'Controller.php';
            $cnt  = count($params) -1;

            while (!file_exists($class_file) && ($ix < $cnt)){
                $ix++;
                $folder = implode(DIRECTORY_SEPARATOR, array_slice($params,0,$ix)). DIRECTORY_SEPARATOR;

                if (is_numeric($params[$ix])){
                    break;
                }

                $controller = $params[$ix];
                $class_file =  CONTROLLERS_PATH. $folder. Strings::snakeToCamel($controller).'Controller.php';
            }

            $action = $params[$ix+1] ?? null;
            $args = array_slice($params,$ix+2);

            $method = !empty($action) ? $action : 'index';

            $class_name = Strings::snakeToCamel($controller);
            $class_name = "{$namespace}{$folder}{$class_name}Controller";
        }

        $class_name = str_replace('/', "\\", $class_name);

        return [$class_name, $method, $args];
    }
}

<?php

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Msg;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;

class AuthHandler
{
    /**
     * Resolve auth routes (/auth)
     *
     * @param array $params
     * @return array [$className, $methodName, $arguments, $api_version]
     */
    public function resolve(array $params): array
    {
        $config = Config::get();
        $res = Response::getInstance();
        $req = Request::getInstance();
        $sub = (int) $config['remove_api_slug'];

        $namespace = namespace_url() . '\\Controllers\\';

        $action = $params[3 - $sub] ?? null;
        $args = array_slice($params, 4 - $sub);

        $method = !empty($action) ? $action : 'index';

        $class_name = 'MyAuth';
        $class_name = "{$namespace}{$class_name}Controller";

        $api_version = $params[1 - $sub];

        if (!preg_match('/^v[0-9]+(\.+[0-9]+)?$/', $api_version, $matches) ){
            $res->error(Msg::INVALID_FORMAT_API_VERSION['text']);
        }

        $class_name = str_replace('/', "\\", $class_name);

        return [$class_name, $method, $args, $api_version];
    }
}

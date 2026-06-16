<?php

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Msg;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;

class ApiHandler
{
    /**
     * Resolve API routes
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

        if (!isset($params[1 - $sub])){
            $res->error(Msg::MISSING_API_VERSION['text']); // API version is missing
        }

        $api_version = $params[1 - $sub];

        if (!preg_match('/^v[0-9]+(\.+[0-9]+)?$/', $api_version, $matches) ){
            $res->error(Msg::INVALID_FORMAT_API_VERSION['text']);
        }

        $controller_raw = $params[2 - $sub] ?? NULL;

        // snake_case -> PascalCase
        $controller = implode('', array_map('ucfirst', explode('_', (string) $controller_raw)));

        /*
            Controllers de framework (viven en \Core\Api\ en vez de \Controllers\api\).
            Detección robusta: normaliza el slug original a minúsculas y sin guiones bajos,
            así 'trashcan', 'trash_can', 'TrashCan' y 'trashCan' resuelven todos a TrashCan
            (antes solo matcheaba el slug ya transformado, p. ej. 'trashcan' daba 404).
        */
        $framework_controllers = [
            'trashcan'    => 'TrashCan',
            'collections' => 'Collections',
        ];

        $norm = strtolower(str_replace('_', '', (string) $controller_raw));

        if (isset($framework_controllers[$norm])) {
            $namespace  = namespace_url() . '\\Core\\Api\\';
            $controller = $framework_controllers[$norm];
        } else {
            $namespace = namespace_url() . '\\Controllers\\api\\';
        }

        $class_name = $namespace . ucfirst($controller);

        $method = strtolower($req->method());
        $args = array_slice($params,3 - $sub,2);

        $class_name = str_replace('/', "\\", $class_name);

        return [$class_name, $method, $args, $api_version];
    }
}

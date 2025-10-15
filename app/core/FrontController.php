<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Msg;
use Boctulus\Simplerest\Core\Request;

class FrontController
{
    const DEFAULT_ACTION = "index";

    static function resolve()
    {
        global $api_version;

        $config = Config::get();
        $res = Response::getInstance();
        $req = Request::getInstance();

        try {
            // 1. Instanciar handlers desde configuración
            $handlers = [];
            foreach ($config['front_behaviors'] as $key => $class) {
                $handlers[$key] = new $class();
            }

            // 2. Determinar tipo de entorno
            $env = php_sapi_name() === 'cli' ? 'cli' : 'http';

            // 3. Obtener parámetros de la request
            [$params, $is_auth, $is_api] = $handlers['request']->parse($env);

            // Si no hay parámetros, terminar
            if (empty($params)) {
                return;
            }

            // 4. Resolver clase, método y parámetros según el tipo de ruta
            $api_version = null;

            if ($is_auth) {
                [$class_name, $method, $args, $api_version] = $handlers['auth']->resolve($params);
            } elseif ($is_api) {
                [$class_name, $method, $args, $api_version] = $handlers['api']->resolve($params);
            } else {
                [$class_name, $method, $args] = $handlers['request']->resolveController($params);
            }

            // 4.5. Verificar si el controlador pertenece a un package y si FrontController está habilitado
            $packageInfo = Config::getPackageFromClass($class_name);

            if ($packageInfo !== null) {
                // Es un controlador de package, verificar configuración específica
                $packageFrontController = Config::getPackageConfig(
                    $packageInfo['vendor'],
                    $packageInfo['package'],
                    'front_controller',
                    true // Default: habilitado
                );

                if (!$packageFrontController) {
                    // FrontController deshabilitado para este package
                    return;
                }
            }

            // Set parameters in Request object for ArrayAccess
            $req->setParams($args);

            // 5. Validar existencia de clase y método
            if (!class_exists($class_name)){
                $res->error('Class Not Found', 404, "Internal error - controller class $class_name not found");
            }

            if (!method_exists($class_name, $method)){
                if ($env != 'cli' || $method != self::DEFAULT_ACTION){
                    /*
                        Si existe __call(), permite que el método sea manejado mágicamente
                    */
                    if (!method_exists($class_name, '__call')){
                        $res->error("Internal error - method $method was not found in $class_name", 404);
                    }
                } else {
                    // CLI sin método index - no ejecutar
                    exit;
                }
            }

            // 6. Ejecutar el método del controlador
            $controller = new $class_name();

            // Validar callable para API Rest
            if ($is_api && !$is_auth){
                if (!in_array($method, $controller->getCallable())){
                    $res->error("Not authorized for $class_name:$method", 403);
                }
            }

            $data = call_user_func_array([$controller, $method], $args);

            // 7. Procesar salida
            if ($data !== null) {
                $output = $handlers['output']->format($controller, $data);
                $res->set($output);
            }

            // 8. Ejecutar middlewares
            $handlers['middleware']->run($class_name, $method);

            // 9. Enviar respuesta
            $res->flush();
            exit;

        } catch (\Throwable $e) {
            $handlers['error']->handle($e);
        }
    }
}



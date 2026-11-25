<?php

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Response;

class MiddlewareHandler
{
    /**
     * Execute middlewares for given class and method
     *
     * @param string $class
     * @param string $method
     * @return void
     */
    public function run(string $class, string $method): void
    {
        $middlewares = include CONFIG_PATH . 'middlewares.php';
        $res = Response::getInstance();

        foreach($middlewares as $injectable => $mids){
            $_i = explode('@', $injectable);

            $_class_name  = $_i[0];

            if (!is_array($mids)){
                $mids = [ $mids ];
            }

            foreach ($mids as $mid){
                $_method = $_i[1] ??  'index';

                if ($class == $_class_name && ($_method == '__all__'|| $method == $_method)){
                    if (!class_exists($mid)){
                        $res->error(trans("Middleware '$mid' not found"), 404, "Internal error - middleware class $mid not found");
                    }

                    $mid_obj = new $mid();
                    $mid_obj->handle();
                }
            }
        }
    }
}

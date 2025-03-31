<?php

namespace Boctulus\Simplerest\middlewares;

use Boctulus\Simplerest\Core\Middleware;
use Boctulus\Simplerest\Core\Libs\Strings;

class InyectarSaludo extends Middleware
{   
    function handle(){
        $data = response()->get();

        if (is_string($data)){
            if (Strings::startsWith('Hello ', $data)){
                response()->set(preg_replace('/Hello (.*)/', "Hello happy $1", $data,1));
            }
        }
    }
}
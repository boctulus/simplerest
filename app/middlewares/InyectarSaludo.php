<?php

namespace simplerest\middlewares;

use simplerest\core\Middleware;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;

class InyectarSaludo extends Middleware
{   
    function __construct()
    {
        parent::__construct();
    }

    function handle(?callable $next = null){
        $data = $this->res;

        if (is_string($data)){
            if (Strings::startsWith('Hello ', $data)){
                $this->res->set(preg_replace('/Hello (.*)/', "Hello happy $1", $data,1));
            }
        }

        //$next($this->req, $this->res);
    }
}
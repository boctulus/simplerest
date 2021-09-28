<?php

namespace simplerest\middlewares;

use simplerest\core\Middleware;
use simplerest\libs\Factory;
use simplerest\libs\Schema;
use simplerest\core\Model;
use simplerest\libs\DB;
use simplerest\libs\Strings;

class InyectarSaludo extends Middleware
{   
    function __construct()
    {
        parent::__construct();
    }

    function handle(?callable $next = null){
        $data = $this->res->getData();

        if (is_string($data)){
            if (Strings::startsWith('Hello ', $data)){
                $this->res->setData(preg_replace('/Hello (.*)/', "Hello happy $1", $data,1));
            }
        }

        //$next($this->req, $this->res);
    }
}
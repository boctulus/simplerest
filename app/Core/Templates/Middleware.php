<?php

namespace Boctulus\Simplerest\middlewares;

use Boctulus\Simplerest\Core\Middleware;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;

class __NAME__ extends Middleware
{   
    function __construct()
    {
        parent::__construct();
    }

    function handle(){
        $res = $this->res->get();

        // ...

        $this->res->set($res);
    }
}
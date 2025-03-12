<?php

namespace simplerest\middlewares;

use simplerest\core\Middleware;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;

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
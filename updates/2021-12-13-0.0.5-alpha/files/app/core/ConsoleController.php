<?php

namespace simplerest\core;

class ConsoleController extends Controller
{
    function __construct()
    {
        if (php_sapi_name() != 'cli'){
            throw new \Exception("Only cli is allowed");
        }

        parent::__construct();        
    }
}


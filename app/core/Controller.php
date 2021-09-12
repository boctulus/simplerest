<?php

namespace simplerest\core;

use simplerest\libs\DB;
use simplerest\libs\Factory;
use simplerest\traits\ExceptionHandler;

abstract class Controller
{
    use ExceptionHandler;

    protected $callable = [];
    
    function __construct() {
        $this->config = Factory::config();

        if ($this->config['error_handling']) {
            set_exception_handler([$this, 'exception_handler']);
        }
    }

    protected function getConnection() {
        return DB::getConnection();
    }

    function getCallable(){
        return $this->callable;
    }

    function addCallable(string $method){
        $this->callable = array_unique(array_merge($this->callable, [$method]));
    }


}
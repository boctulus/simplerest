<?php

namespace simplerest\core;

class Middleware 
{   
    protected $req;
    protected $res;

    function __construct()
    {
        if ($this->req === null){
            $this->req = request();
        }

        if ($this->res === null){
            $this->res = response();
        }
    }

    function handle(?callable $next = null){
    
    }
}
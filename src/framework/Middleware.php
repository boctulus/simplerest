<?php

declare(strict_types=1);
namespace Boctulus\Simplerest\Core;

abstract class Middleware 
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

    function handle(){
    
    }
}
<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class FakeController extends MyController
{
    // Solo para una prueba
    function parts(){
        header('Content-Type: application/json; charset=utf-8');
        
        return '[
            {"id":1, "name":"bob", "age":"23"},
            {"id":2, "name":"jim", "age":"45"},
            {"id":3, "name":"steve", "age":"32"}
        ]';
    }

    function index()
    {
       $this->parts();                
    }
}


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
            {
                "id": "1",
                "nombre": "px400",
                "nota": "una parte cualquiera",
                "created_at": "2022-11-17 17:23:05",
                "updated_at": "2022-11-17 17:23:05"
            },
            {
                "id": "2",
                "nombre": "ftx600",
                "nota": "bla bla",
                "created_at": "2022-11-17 17:23:05",
                "updated_at": "2022-11-17 17:23:05"
            },
            {
                "id": "3",
                "nombre": "px500",
                "nota": "una parte cualquiera",
                "created_at": "2022-11-17 17:32:17",
                "updated_at": null
            }
        ]';
    }

    function index()
    {
       $this->parts();                
    }
}


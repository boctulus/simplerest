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

    function test05(){
        header('Content-Type: application/json; charset=utf-8');
        
        $data =  [
            ['id' =>1, 'name'=>"Billy Bob", 'progress'=>"12", 'gender'=>"male", 'height'=>1, 'col'=>"red", 'dob'=>"", 'driver'=>1],
            ['id' =>2, 'name' =>"Mary May", 'progress' =>"1", 'gender' =>"female", 'height' =>2, 'col' =>"blue", 'dob' =>"14/05/1982", 'driver' =>true],
            ['id' =>3, 'name' =>"Christine Lobowski", 'progress' =>"42", 'height' =>0, 'col' =>"green", 'dob' =>"22/05/1982", 'driver' =>"true"],
            ['id' =>4, 'name' =>"Brendon Philips", 'progress' =>"125", 'gender' =>"male", 'height' =>1, 'col' =>"orange", 'dob' =>"01/08/1980"],
            ['id' =>5, 'name' =>"Margret Marmajuke", 'progress' =>"16", 'gender' =>"female", 'height' =>5, 'col' =>"yellow", 'dob' =>"31/01/1999"],
        ];

        return [
            "last_page"=>30, 
            "data"=>$data
        ];
    }

    function index()
    {
       $this->parts();                
    }
}


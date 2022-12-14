<?php

namespace simplerest\controllers\bt5;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class ValidationController extends MyController
{
    /*
        Basico  
    */
    function index(){
        view('hello/validation');
    }

    function v1(){
        view('hello/v1');
    }

    function v2(){
        view('hello/v2');
    }

    function v2a(){
        view('hello/v2a');
    }

    function v3(){
        view('hello/v3');
    }

}


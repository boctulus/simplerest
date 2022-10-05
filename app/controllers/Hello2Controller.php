<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Time;

class Hello2Controller extends Controller
{
    function index(){
        view('hello/hello2.php', null, null, 15);
    }

    function lte(){
        view('hello/lte.php', null, null, 15);
    }
    
    function test(){
        view('hello/test.php');
    }

   
}


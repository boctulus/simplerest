<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Time;

class Hello2Controller extends Controller
{
    function index(){
        $this->view('hello/hello2.php', null, null, 0);
    }

    function lte(){
        $this->view('hello/lte.php');
    }
    
    function test(){
        $this->view('hello/test.php');
    }

   
}


<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Time;

class Hello2Controller extends Controller
{
    function index(){
        $this->view('hello/hello2.php', null, null, 0);
    }

    function acordion(){
        $this->view('hello/acordion.php');
    }

    function test(){
        $this->view('hello/test.php');
    }

    function filter(){
        $this->view('excel/filter.php');
    }
}


<?php

namespace simplerest\controllers\bt5;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Time;

class Hello2Controller extends Controller
{
    function index(){
        view('hello/hello2', null, null, 15);
    }

    function lte(){
        view('hello/lte', null, null, 15);
    }
    
    function test(){
        view('hello/test');
    }

    function test_multiple(){
        view('hello/multiple');
    }
   
}


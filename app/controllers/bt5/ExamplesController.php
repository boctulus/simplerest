<?php

namespace simplerest\controllers\bt5;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Time;

class ExamplesController extends Controller
{
    function index(){
        view('html_builder_examples/hello2', null, null, 15);
    }

    function lte(){
        view('html_builder_examples/lte', null, null, 15);
    }
    
    function test(){
        view('html_builder_examples/test');
    }

    function test_multiple(){
        view('html_builder_examples/multiple');
    }
   
}


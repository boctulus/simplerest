<?php

namespace simplerest\controllers\html_builder;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Time;

class HtmlDemo2Controller extends Controller
{
    /*
        De momento NO usar cache !!!
    */

    function __construct()
    {
        js_file(ASSETS_PATH . 'js/popper.min.js', null, true);
    }

    function index(){
        view('html_builder_examples/hello2');
    }

    function lte(){
        view('html_builder_examples/lte');
    }
    
    function test(){
        view('html_builder_examples/test');
    }

    /*
        Componente (a encapsular) que muestra selector de categorias (para WordPress)
    */
    function test_multiple(){
        view('html_builder_examples/multiple');
    }
   
}


<?php

namespace Boctulus\Simplerest\Controllers\html_builder;

use Boctulus\Simplerest\Core\View;
use Boctulus\Simplerest\Core\Controllers\Controller;

class Select2Controller extends Controller
{
    function __construct()
    {
        parent::__construct(); 

        css_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css');
        css_file('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css');

        js_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js');
    }

    function index()
    { 
        $this->test1();
    }

    /*
        Countries & states
    */
    function test1()
    {   
        view('select2/test1.php',[], 'templates/tpl.php');              
    }
    
    function test_colorchange(){
        view('select2/test_color-change.php', [
            []
        ], 'templates/tpl.php');        
    }

    // Ejemplo practico
    function test_colorchange2(){
        view('select2/test_color-change-2.php', [
            []
        ], 'templates/tpl.php');        
    }

    function test2()
    {   
        $json  = file_get_contents(ETC_PATH . 'countries_states.json');

        view('select2/test2.php', [
            'json' => $json
        ], 'templates/tpl.php');              
    }
}


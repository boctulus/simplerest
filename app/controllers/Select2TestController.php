<?php

namespace simplerest\controllers;

use simplerest\core\View;
use simplerest\controllers\MyController;

class Select2TestController extends MyController
{
    function __construct()
    {
        parent::__construct(); 
    }

    /*
        Countries & states
    */
    function index()
    {   
        $json  = file_get_contents(ETC_PATH . 'countries_states.json');

        css_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css');
        css_file('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css');

        js_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js');

        view('select2/test.php', [
            'json' => $json
        ], 'tpl_basic.php');              
    }
    
    /*
        Se requiere form original => acceso al servidor

        https://diagnosticojournal.us8.list-manage.com/subscribe?u=a981b45880eea70b899f8ed96&id=363f983b38
    */
    function custom()
    {   
        $json  = file_get_contents(ETC_PATH . 'countries_states.json');

        View::css_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css');
        View::css_file('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css');

        View::js_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js');

        view('select2/diagnosticojournal.php', [
            'json' => $json
        ], 'tpl_basic.php');              
    }
}


<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;

class Select2TestController extends MyController
{
    function __construct()
    {
        parent::__construct(); 
    }

    /*
        Integrar en

        https://diagnosticojournal.us8.list-manage.com/subscribe?u=a981b45880eea70b899f8ed96&id=363f983b38
    */
    function index()
    {   
        $json  = file_get_contents(ETC_PATH . 'countries_states.json');

        // Js ofuscado con https://obfuscator.io/
        view('select2/test.php', [
            'json' => $json,
            'head' => [
                'css' => [
                    'https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css',
                    'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css'
                ]
                ],
            'footer' => [
                'js' => [
                    'https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js'
                ]
            ]
        ], 'tpl_basic.php');              
    }
    
}


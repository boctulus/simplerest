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

        Desarrollo para Gabriel. 
        +54 9 11 6857-1750

        Countries & states
    */
    function index()
    {   
        $json  = file_get_contents(ETC_PATH . 'countries_states.json');

        // $all = json_decode($json, true)["countries"];

        // dd(
        //     implode(', ',array_column($all, 'country'))
        // );

        // exit;

        // dd(count(
        //     json_decode($json, true)["countries"]
        // ));

        // exit;


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
    
    /*
        Se requiere form original => acceso al servidor

        https://diagnosticojournal.us8.list-manage.com/subscribe?u=a981b45880eea70b899f8ed96&id=363f983b38
    */
    function custom()
    {   
        $json  = file_get_contents(ETC_PATH . 'countries_states.json');

        view('select2/diagnosticojournal.php', [
            'json' => $json,
            'head' => [
                'css' => [
                    'https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css',
                    'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css'
                ],
                'js' => [
                    'https://twemoji.maxcdn.com/2/twemoji.min.js',
                    asset('js/dojo/dojo.js')
                ]
            ],
            'footer' => [
                'js' => [
                    'https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js',
                    'https://diagnosticojournal.us8.list-manage.com/jlruc/pJ/v/V/gOBW3Jhn-J0/EGV3cLJJOX/BxRDUWU/Mlg/dVh8fUg0'
                ]
            ]
        ], 'tpl_basic.php');              
    }
}


<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

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

        view('select2/test.php', [
            'json' => $json
        ]);              
    }
    
}


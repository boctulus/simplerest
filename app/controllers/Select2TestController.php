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

    function index()
    {   
        $json  = file_get_contents(ETC_PATH . 'countries_states.json');
        // $tutto = json_decode($json, true)['countries']; // paises-provincias

        view('select2/test.php', [
            'json' => $json
        ]);              
    }
    
}


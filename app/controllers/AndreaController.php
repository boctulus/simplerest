<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class AndreaController extends MyController
{
    function index()
    {
        css_file(
            asset('andrea/css/theme.css')
        );

        css_file(
            asset('andrea/css/master.css')
        );


        view('andrea/builder');            
    }
}


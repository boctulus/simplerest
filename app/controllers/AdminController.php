<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class AdminController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        $content = 'Hola mundo!';

        render($content, 'templates/adminlte_tpl.php', [
            'brandname' => 'Planex',
            'logo'       => 'img/planex_logo.png',
            'logo_alt'   => 'Planex'
        ]);            
    }
}


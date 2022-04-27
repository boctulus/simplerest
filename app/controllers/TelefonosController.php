<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class TelefonosController extends MyController
{
    function __construct()
    {
        DB::getConnection('ef');
        parent::__construct();        
    }

    function index()
    {
        $rows = DB::table('telefonos')->get();   
        
        view('telefonos/listar_telefonos.php', [
            'agendados' => $rows
        ]);
    }
}


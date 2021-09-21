<?php

namespace simplerest\controllers;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;

class PruebaController extends Controller
{
    function __construct()
    {
        parent::__construct();        
    }

    function consumir(){
        DB::getConnection('db_flor');

        $res = DB::table('tbl_descuento')
        ->join('tbl_estado')
        ->get();
        
        dd($res);
    }
}


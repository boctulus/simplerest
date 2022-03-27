<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class SslController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        $rows = DB::table('ssl')
        ->orderBy(['expires_at' => 'DESC'])
        ->get();
        
        d($rows);
    }

     /*
        @param $domain string dominio o subdominio
        @param $expires_in int dias para expiración
    */
    function add(string $domain, int $expires_in){
        $d1 = new \DateTime();
        $d2 = $d1->modify("+$expires_in days")->format('Y-m-d H:i:s');

        DB::getDefaultConnection();

        $res = DB::table('ssl')
        ->create([
            'domain'     => $domain,
            'expires_at' => $d2
        ]);
        //dd(DB::getLog());
    }

    function renew(string $domain, int $expires_in){
        $d1 = new \DateTime();
        $d2 = $d1->modify("+$expires_in days")->format('Y-m-d H:i:s');

        DB::getDefaultConnection();

        $res = DB::table('ssl')
        ->where([
            'domain' => $domain
        ])
        ->update([
            'expires_at' => $d2
        ]);

        //dd(DB::getLog());
    }
}


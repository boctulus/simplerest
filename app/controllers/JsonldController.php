<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyController;
use simplerest\core\libs\JsonLd;

class JsonldController extends MyController
{
    function jsonld_extractor(){

    }

    function run()
    {
        $prod_url = $_GET['url']; // url='https://www.giglio.com/scarpe-uomo_sneakers-alexander-mcqueen-586198whx52.html?cSel=002'
 
        $cli = (new ApiClient($prod_url))
        ->withoutStrictSSL()
        ->setHeaders([
            'User-Agent' => 'PostmanRuntime/7.34.0',
        ])
        ->cache(600000);

        $cli->setMethod('GET');
        $cli->send();
        $res = $cli->data();
        
        $data = JsonLd::extract($res);

        dd($data);                 
    }
}


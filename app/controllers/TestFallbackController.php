<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use Boctulus\ApiClient\ApiClientFallback as ApiClientFallbackAlias;

class TestFallbackController extends Controller
{
    function __construct() { parent::__construct(); }

    function index(){
        dd(__CLASS__, 'CLASS');

        $client = new ApiClientFallbackAlias();

        $client
        ->cache(3);

        $client
        ->setHeaders([
            "Content-type" => "application/json"
        ])
        ->get(base_url() . '/dumb/now');

        $res = $client->getResponse();

        dd($res, 'RESPONSE');

        dd($client->status(), 'STATUS');
        dd($client->error(), 'ERROR');
        
        dd($client
        // ->decode()
        ->data(), 'DATA');
    }
}

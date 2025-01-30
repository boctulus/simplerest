<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use Boctulus\ApiClient\ApiClientFallback as ApiClientFallbackAlias;

class TestFallbackController extends Controller
{
    function __construct() { parent::__construct(); }

    function test_apiclientfallback_package(){
        $client = new ApiClientFallbackAlias();

        $client
        ->setHeaders([
            "Content-type" => "application/json"
        ])
        ->get("https://jsonplaceholder.typicode.com/posts/1");

        $res = $client->getResponse();

        dd($client->status(), 'STATUS');
        dd($client->error(), 'ERROR');
        dd($client
        ->decode()
        ->data(), 'DATA');
    }
}

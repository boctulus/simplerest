<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;

use Symfony\Component\HttpClient\HttpClient;

class SymfonyHttpTestController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function test()
    {
        $httpClient = HttpClient::create();
                 
        $response = $httpClient->request('POST', 'http://woo5.lan/wp-login.php', [
            'body' => [
                'log' => 'boctulus',
                'pwd' => '!0EJEbwu)Oa!3Fd&ev',
                'rememberme' => 'forever',
                'redirect_to' => 'http://woo5.lan/my-account/',
                'redirect_to_automatic' => '1'
            ]
        ]);

        
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        // Obtener las cookies establecidas en la sesión
        $cookies = $response->getCookies();

        $response = $httpClient->request('GET', 'http://woo5.lan/my-account/');
    }
}


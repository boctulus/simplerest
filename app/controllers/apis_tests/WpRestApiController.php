<?php

namespace simplerest\controllers\apis_tests;

use simplerest\core\libs\DB;
use simplerest\core\libs\WooCommerceRestAPI;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyController;


/*
    Creacion de credenciales para Basic Auth en la REST API de WooCommerce

    Ir a /wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys

    Notas:

    Authentication Methods

    For the newer APIs (not the "legacy" APIs):

    . For secure connections (HTTPS), you need to use "Basic Authentication".
    
    . For non-secure connections (HTTP), you need to use "oAuth1 Authentication".  <--------- !!!
*/
class WpRestApiController extends MyController
{   
    // apis_tests wp_rest_api
    function index()
    {
        $base_url = 'http://woo1.lan';
        $endpoint = '/wp-json/wc/v3/products';
        $method   = 'GET';
                       
        $url    = "{$base_url}{$endpoint}";
  
        $request = array(
            'method' => $method,
            'url'    => $url
        );

        $woo = new WooCommerceRestAPI('ck_185ddf7e2fa4f631b8a460f3963b1dc818bc5abf', 'cs_50688595c64b40a0cc1bb610b3852c9ab43be245');

        $cli = (new ApiClient($url))
        ->withoutStrictSSL()
        ->setHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $woo->setOAuth( $request),
            'User-Agent'   => 'PostmanRuntime/7.34.0',
        ])

        // ->queryParams([
        //     'consumer_key'    => $consumer_key,
        //     'consumer_secret' => $consumer_secret
        // ])       
        
        //->cache(600000)
        ;

        $cli->setMethod('GET');

        $cli->send();

        dd($cli->dump(), 'REQ');

        $res = $cli->data();

        // {"errors":[{"code":"woocommerce_api_authentication_error","message":"oauth_consumer_key parameter is missing"}]}
        dd($res, 'RES');
    }
}


<?php

namespace simplerest\controllers\api_tests;

use simplerest\core\libs\DB;
use simplerest\core\libs\WooCommerceApiClient;
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
    // api_tests wp_rest_api get_products
    function get_products()
    {
        $base_url = 'http://woo1.lan';
        $endpoint = '/wp-json/wc/v3/products';
                       
        $url      = "{$base_url}{$endpoint}";
  
        $woo_cli  = new WooCommerceApiClient('ck_185ddf7e2fa4f631b8a460f3963b1dc818bc5abf', 
        'cs_50688595c64b40a0cc1bb610b3852c9ab43be245');

        $woo_cli
        ->url($url)
        ->get()
        ->setOAuth();

        $woo_cli->send();

        dd($woo_cli->dump(), 'REQ');

        $res = $woo_cli->data();

        dd($res, 'RES');
    }

    // api_tests wp_rest_api create_product
    function create_product()
    {
        $base_url = 'http://woo1.lan';
        $endpoint = '/wp-json/wc/v3/products';
                       
        $url      = "{$base_url}{$endpoint}";

        $product  = ETC_PATH . 'woocommerce/product_rest_api.php'; //// <------ colocar ahi
                    
  
        $woo_cli  = new WooCommerceApiClient('ck_185ddf7e2fa4f631b8a460f3963b1dc818bc5abf', 
        'cs_50688595c64b40a0cc1bb610b3852c9ab43be245');

        $woo_cli
        ->url($url)
        ->get()
        ->setOAuth();

        $woo_cli->setBody($product); //

        $woo_cli->send();

        dd($woo_cli->dump(), 'REQ');

        $res = $woo_cli->data();

        dd($res, 'RES');
    }
}


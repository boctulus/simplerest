<?php

namespace Boctulus\Simplerest\Controllers\api_tests;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\WooCommerceApiClient;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Arrays;

/*
    Creacion de credenciales para Basic Auth en la REST API de WooCommerce

    Ir a /wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys

    Notas:

    Authentication Methods

    For the newer APIs (not the "legacy" APIs):

    . For secure connections (HTTPS), you need to use "Basic Authentication".
    
    . For non-secure connections (HTTP), you need to use "oAuth1 Authentication".  <--------- !!!
*/
class WooRestApiController extends Controller
{   
    protected function getClient(){
        return new WooCommerceApiClient('ck_f710ad18c309b89f309e7144da238814bd4bf6b4', 
        'cs_53b05e639bdba922eb296fb7ab40e162eb7570d6');
    }

    // api_tests wp_rest_api get_products
    function get_products($pid)
    {
        $base_url = 'http://woo9.lan';
        $endpoint = '/wp-json/wc/v3/products' . (!empty($pid) ? "/$pid" : '');
                       
        $url      = "{$base_url}{$endpoint}";
  
        $woo_cli  = $this->getClient();

        $woo_cli
        ->url($url)
        ->get()
        ->setOAuth();

        $woo_cli->cache(85000 * 7); // <-- AJUSTAR

        $woo_cli->send();

        dd($woo_cli->dump(), 'REQ');

        $res = $woo_cli->data();

        return $res;
    }

    function toRestApiArray(Array $product){
        $arr = $product;

        // ..

        return $arr;
    }

    // api_tests wp_rest_api create_product
    function create_product()
    {
        $base_url = 'http://woo9.lan';
        $endpoint = '/wp-json/wc/v3/products';
                       
        $url      = "{$base_url}{$endpoint}";

        $filename = ETC_PATH . 'woocommerce/product_rest_api-1.php'; //// <------ colocar ahi
        $product  = require $filename;

        // dd($product);
        
        $woo_cli  = $this->getClient();

        $woo_cli
        ->url($url)
        ->post()  ///
        ->setOAuth();

        $woo_cli->setBody($product); //

        $woo_cli->send();

        dd($woo_cli->dump(), 'REQ');

        $res = $woo_cli->data();

        dd($res, 'RES');

        return $res['id'] ?? null;
    }

    function create_catego()
    {
        $base_url = 'http://woo9.lan';
        $endpoint = '/wp-json/wc/v3/products/categories';
                       
        $url      = "{$base_url}{$endpoint}";

        $filename = ETC_PATH . 'woocommerce/product_cat_rest_api.php'; //// <------ colocar ahi
        $product  = require $filename;

        // dd($product);
        
        $woo_cli  = $this->getClient();

        $woo_cli
        ->url($url)
        ->post()  ///
        ->setOAuth();

        $woo_cli->setBody($product); //

        $woo_cli->send();

        $res = $woo_cli->data();

        return $res['id'] ?? null;
    }

    /*
        Crea variacion de un producto
    */
    function create_variation($pid)
    {
        $base_url = 'http://woo9.lan';
        $endpoint = '/wp-json/wc/v3/products/' . $pid . '/variations';
                       
        $url      = "{$base_url}{$endpoint}";

        $filename = ETC_PATH . 'woocommerce/product_var_rest_api.php'; //// <------ colocar ahi
        $product  = require $filename;

        // dd($product);
        
        $woo_cli  = $this->getClient();

        $woo_cli
        ->url($url)
        ->post()  ///
        ->setOAuth();

        $woo_cli->setBody($product); //

        $woo_cli->send();

        $res = $woo_cli->data();

        dd($res);

        return $res['id'] ?? null;
    }

    function get_variations($pid, $variation_id = null)
    {
        $base_url = "http://woo9.lan";
        $endpoint = "/wp-json/wc/v3/products/$pid/variations" . (!empty($variation_id) ? "/$variation_id" : '');
                       
        $url      = "{$base_url}{$endpoint}";
  
        $woo_cli  = $this->getClient();

        $woo_cli
        ->url($url)
        ->get()
        ->setOAuth();

        $woo_cli->cache(85000 * 7); // <-- AJUSTAR

        $woo_cli->send();

        // dd($woo_cli->dump(), 'REQ');

        $res = $woo_cli->data();

        var_export($res);

        return $res;
    }

    function test(){
        // dd($this->get_variations(16958));

        // exit;

        // dd($this->get_products(16911));

        // exit;
        
        dd($this->create_variation(16958));
    }
}


<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\WooCommerceApiClient;
use simplerest\controllers\MyController;
use simplerest\core\libs\Arrays;

/*
    Este es un controlador de testing, no es una libreria y en todo caso
    lo que se deberia hacer es pasar parte del codigo a la libreria


    Creacion de credenciales para Basic Auth en la REST API de WooCommerce

    Ir a /wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys

    Notas:

    Authentication Methods

    For the newer APIs (not the "legacy" APIs):

    . For secure connections (HTTPS), you need to use "Basic Authentication".
    
    . For non-secure connections (HTTP), you need to use "oAuth1 Authentication".  <--------- 

    TO-DO

    - Crear adapter para Products::dump(), Products::create() y Products::update()
    con los arrays requeridos por los endpoints de la API oficial de WooCommerce
*/
class WooRestApiController extends MyController
{   
    function __construct(){
        header('Content-Type: application/json; charset=utf-8');
    }

    protected function getClient(){
        return new WooCommerceApiClient('ck_f095140fb70fe12c4c73e2003e81b5be2e5a2ebf', 
        'cs_515b02e9d709d752855f54ee1a3f7aede03012d5');
    }

    // php com WooRestApi get_products
    function get_products($pid = null)
    {
        $base_url = 'http://luxuritop.test';
        $endpoint = '/wp-json/wc/v3/products' . (!empty($pid) ? "/$pid" : '');
                       
        $url      = "{$base_url}{$endpoint}";
  
        $woo_cli  = $this->getClient();

        $woo_cli
        ->url($url)
        ->get()
        ->setOAuth();

        /*
            Si hay error la CACHE no se activa (por diseno)
        */

        $woo_cli->cache(85000 * 7); // <-- AJUSTAR

        $woo_cli->send();

        // dd($woo_cli->dump(), 'REQ');

        $res = $woo_cli->data();

        return $res;
    }

    function toRestApiArray(Array $product){
        $arr = $product;

        // ..

        return $arr;
    }

    /*
        wp_rest_api create_product

        La API de WordPress tiene varios problemas, entre ellos necesita que la url de la imagen
        tenga una extension reconocible como .png y sino es asi:

        --| RES
        array (
            'code' => 'woocommerce_product_image_upload_error',
            'message' => 'Immagine non valida: Non hai i permessi per caricare questo tipo di file.',
            'data' => 
            array (
                'status' => 400,
            ),
        )

        Como paso final toca crear las variaciones.
    */
    function create_product()
    {
        $base_url = 'http://luxuritop.test';
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

    /*
        Crea variacion de un producto
    */
    function create_variation($pid)
    {
        $base_url = 'http://luxuritop.test';
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
        $base_url = "http://luxuritop.test";
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
    
    function create_catego()
    {
        $base_url = 'http://luxuritop.test';
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

    function test(){
        // dd($this->get_variations(16958));

        // exit;

        // dd($this->get_products(16911));

        // exit;
        
        dd($this->create_variation(16958));
    }
}


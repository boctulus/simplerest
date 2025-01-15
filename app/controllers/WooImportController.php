<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\WooCommerce;
use simplerest\core\traits\TimeExecutionTrait;

class WooImportController extends Controller
{
    function __construct() { parent::__construct(); }

    // En tu controlador
    function index() {
         // Parámetros
        $limit = $_GET['limit'] ?? 10;
        $offset = $_GET['offset'] ?? 0;
        $simulate = $_GET['simulate'] ?? 0;
         
        $cfg = require CONFIG_PATH . 'woocommerce.php';
        $woo = new WooCommerce($cfg);
        
        // Lee JSON  --- ejemplo
        $path        = 'D:\laragon\www\woo9\wp-content\plugins\perfumes_data-sync\etc\product_jsons/aaz-midghoudedph.json';
        $jsonProduct = json_decode(file_get_contents($path), true);
        
        // Crea el producto principal
        $productData = $woo->transformProductData($jsonProduct);

        // dd($productData); exit; //

        $response = $woo->createProduct($productData);
        
        if (isset($response['id'])) {
            $productId = $response['id'];
            
            // Crea las variaciones
            foreach ($jsonProduct['variations'] as $variation) {
                $woo->createVariation($productId, $variation);
            }
        }
    }
}


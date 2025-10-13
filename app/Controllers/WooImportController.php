<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\WooCommerce;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

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


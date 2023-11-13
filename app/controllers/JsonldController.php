<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyController;
use simplerest\core\libs\JsonLd;

class JsonldController extends MyController
{   
    function getProductBasicAttr($html){
        $dom = new \DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        // Crear un objeto DOMXPath para realizar consultas XPath en el documento
        $xpath = new \DOMXPath($dom);

        // Array para almacenar los datos extraídos
        $productDetails = array();

        // Extraer nombre del producto
        $productNameNode = $xpath->query('//h1[@class=""]')->item(0);
        if ($productNameNode) {
            $productDetails['product_name'] = trim($productNameNode->nodeValue);
        }

        // Extraer descripción del producto
        $descriptionNode = $xpath->query('//section[contains(@class, "product_editorial")]//p')->item(0);
        if ($descriptionNode) {
            $productDetails['desc'] = trim($descriptionNode->nodeValue);
        }

        if (Strings::contains('•', $productDetails['desc'])){
            $desc_items = Strings::lines($productDetails['desc'], true, true, '•');
        }

        $desc_items = array_map(function($item) {
            return "<li>$item</li>\r\n";
        }, $desc_items);
      
        // debe ser para cada elemento. array_walk ??
        $productDetails['desc'] = "<ol>\r\n". implode('', $desc_items) . "</ol>\r\n";
    
        return $productDetails;
    }

    function run()
    {
        $prod = [];

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

        if (empty($data)){
            die('No hay JSON-LD ?');
        }

        /*
            Armo la respuesta
        */

        // nombre de producto,..
        $prod = $this->getProductBasicAttr($res);

        $prod['availability'] = str_replace('https://schema.org/', '', $data[0]['offers']['availability']) ?? null;   

        $prod['attr'] = [];
        $prod['attr']['brand']    = $data[0]['brand']['name'] ?? null;  
        $prod['attr']['currency'] = $data[0]['offers']['priceCurrency'] ?? null;  
        $prod['attr']['price_until'] = $data[0]['offers']['priceValidUntil'] ?? null;   
        $prod['attr']['condition'] = str_replace('https://schema.org/', '', $data[0]['offers']['itemCondition']) ?? null;   
        $prod['attr']['seller'] = $data[0]['offers']['seller']['name'] ?? null; 

        // procesamientos adicionales 
        $prod['attr']['condition'] = str_replace('Condition', '', $prod['attr']['condition']);

        //variations
        $prod['vs'] = [];

        foreach ($data as $p){
            // variation

            $prod['vs'][] = [                
                'name'         => $p['name'] ?? null,
                'sku'          => $p['sku'] ?? null,
                'description'  => $p['description'] ?? null,
                'images'       => $p['image'] ?? null,
                'price'        => $p['offers']['price'] ?? null,
                
                'attr'         => [
                    'mpn'   => preg_replace('/[^a-zA-Z0-9.\-_]/', '', $p['mpn']) ?? '',
                    'color' => $p['color'] ?? '',
                ]
            ];
        }
        

        dd($prod);                 
    }
}


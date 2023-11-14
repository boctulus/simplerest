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

    /*
        Obtiene categoria de producto indidual

        Hay muchas inconcistncias con el slug que a veces se halla dentro de un enlace y otras veces dentro de un span asi que no se considera
    */
    static protected function getCatego($html){
        $dom = new \DOMDocument;
        
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
    
        // Encuentra el último elemento 'li' dentro de la lista de migas de pan
        $lastLiElement = $xpath->query('//ol[@class="breadcrumbs"]/li[last()]')->item(0);
    
        if ($lastLiElement) {
            // Limpiar el contenido del nodo de texto dentro de 'span'
            $name = trim($xpath->evaluate('string(./span/text())', $lastLiElement));

            // $linkElements = $xpath->query('//ol[@class="breadcrumbs"]/li/a[@itemprop="item"]', $lastLiElement);

            // if ($linkElements->length > 0) {
            //     // Obtener el valor del atributo 'href' del último enlace
            //     $lastLinkElement = $linkElements[$linkElements->length - 1];
            //     $href = $lastLinkElement->getAttribute('href');

            //     // Formar el slug eliminando la parte inicial de la URL
            //     $slug = preg_replace('#https://www\.giglio\.com#', '', $href);
            // }
            
            
            // $result = [
            //     // 'slug' => $slug,
            //     'name' => $name,
            // ];
    
            return $name;
        }
    
        return null; // Devolver null si no se encuentra el elemento
    }    
    
    static function getCategos($html){
        $dom = new \DOMDocument;
    
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
    
        $categoryElements = $xpath->query('//li[contains(translate(span/text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "categoria") or contains(translate(span/text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "categoria")]');
    
        $categos = [];
    
        foreach ($categoryElements as $categoryElement) {
            $categoryName = $xpath->evaluate('string(span)', $categoryElement);
            $categoryElements = $xpath->query('.//li[.//a]', $categoryElement);
    
            if ($categoryElements->length > 0) {
                foreach ($categoryElements as $categoryElement) {
                    $categoryName = $xpath->evaluate('string(a)', $categoryElement);
                    $categorySlug = $categoryElement->getElementsByTagName('a')->item(0)->getAttribute('href');

                    $categoryName = trim($categoryName);
    
                    $categos[] = [
                        'slug' => $categorySlug,
                        'name' => $categoryName
                    ];
                }
            } else {
                // No hay categorías, solo la categoría principal
                $categorySlug = $categoryElement->getElementsByTagName('a')->item(0)->getAttribute('href');

                if (empty($categorySlug)){
                    continue;
                }
                
                $categoryName = trim($categoryName);

                $categos[] = [
                    'slug' => $categorySlug,
                    'name' => $categoryName
                ];
            }
        }

         // data processing

         foreach ($categos as $ix => $cat) {
            $cat['name'] = trim($cat['name']);

            // por alguna razon a veces falla y lo deja pasar
            if (Strings::contains('Vedi tutt', $cat['name'])){
                $categos[$ix] = null;
            }

        }
    
        return $categos;
    }

    /*
        No guardar estas categorias pero usarlas para recuperar productos

        Luego se puede obtener la categoria dentro de cada pagina de producto
    */
    static function getBrands($html){
        $dom = new \DOMDocument;
    
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
    
        $brandElements = $xpath->query('//li[contains(translate(span/text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "designer")]');
    
        $brands = [];
    
        foreach ($brandElements as $brandElement) {
            $name = $xpath->evaluate('string(span)', $brandElement);
            $categoryElements = $xpath->query('.//li[.//a]', $brandElement);
    
            if ($categoryElements->length > 0) {
                foreach ($categoryElements as $categoryElement) {
                    $name = $xpath->evaluate('string(a)', $categoryElement);
                    $slug = $categoryElement->getElementsByTagName('a')->item(0)->getAttribute('href');

                    if (empty($slug)){
                        continue;
                    }

                    $name = trim($name);
    
                    $brands[] = [
                        'slug' => $slug,
                        'name' => $name
                    ];
                }
            } else {
                // No hay subcategorías, solo la categoría principal
                $slug = $brandElement->getElementsByTagName('a')->item(0)->getAttribute('href');

                if (empty($slug)){
                    continue;
                }

                $name = trim($name);

                $brands[] = [
                    'slug' => $slug,
                    'name' => $name
                ];
            }
        }
    
        // data processing

        foreach ($brands as $ix => $brand) {
            if (empty($brand) || !is_array($brand) || !array_key_exists('name', $brand)) {
                unset($brands[ $ix ]);
            }

            $brand['name'] = trim($brand['name']);            
            $brand['name'] = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $brand['name']);

            // por alguna razon a veces falla y lo deja pasar
            if (Strings::contains('Vedi tutt', $brand['name'], false)){
                $brands[$ix] = null;
            }

        }

        return $brands;
    }    
    

    static protected function getCategosList($html) {
        dd(static::getBrands($html));

        exit;
        dd(static::getCategos($html));
        // exit; 
        
        // ...
    }

    static protected function getHTML($url, $exp_time = 21600){
        $cli = (new ApiClient($url))
        ->withoutStrictSSL()
        ->setHeaders([
            'User-Agent' => 'PostmanRuntime/7.34.0',
        ])
        ->cache($exp_time);

        $cli->setMethod('GET');
        $cli->send();
        $res = $cli->data(); // html

        return $res;
    }

    /*
        url='https://www.giglio.com/scarpe-uomo_sneakers-alexander-mcqueen-586198whx52.html?cSel=002'
    */
    function run()
    {
        $prod = [];

        $url  = $_GET['url']; 

        $html = static::getHTML($url, 600000);
        
        dd(static::getCatego($html));
        exit; ///

        $data = JsonLd::extract($html);

        if (empty($data)){
            die('No hay JSON-LD ?');
        }

        static::getCategosList($html);
        exit; //

        /*
            Armo la respuesta
        */

        // nombre de producto,..
        $prod = $this->getProductBasicAttr($html);

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


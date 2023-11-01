<?php

namespace simplerest\libs\scrapers;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Dom;
use simplerest\core\libs\Url;
use simplerest\core\libs\Files;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\core\libs\XML;

class Curiosite
{
    static function parseProduct(string $ori){
        $is_url = false;

        if (Strings::startsWith('http', $ori)){
            /*
                Es una url
            */

            $url    = $ori;
            $is_url = true;

            $fs = explode('/', parse_url($url, PHP_URL_PATH));

            if (count($fs)<2){
                throw new \InvalidArgumentException("Url inválida: no hay slug?");
            }
            
            $slug = Url::lastSlug($url);

            if (!isset($slug)){
                throw new \InvalidArgumentException("Url inválida: no hay slug?");
            }

            $res = (new ApiClient($url))->disableSSL()
            ->followLocations()
            //->clearCache()
            //->cache()
            ->get()
            ->getResponse(false);

            if ($res['http_code'] != 200){
                return;
            }

            $html = $res['data'];
            
        } else {
            /*
                Es un archivo local
            */

            if (!file_exists($ori)){
                return;
            }

            $uri  = str_replace(BASE_URL, '', $ori);
            $uri  = str_replace('p2F', '/', $uri);
            $uri  = str_replace('p3F', '/', $uri);
            $slug = substr($uri, 0, strlen($uri) -5);

            //$slug = Strings::exceptLastChar(Strings::segment($ori, '2F', 4));
            $html = file_get_contents($ori);
        }

        if (empty($html)){
            return;
        }

        // Verifico que SI sea una página de producto (detalle)

        if ($is_url){
            if (!Strings::contains('/producto/', $ori)){
                Logger::log("Parsing of $ori as failed");
                return;
            }
        } else {
            if (!Strings::contains('product-details', $html)){
                Logger::log("Parsing of $ori as failed");
                return;
            }
        }

      

        $doc   = XML::getDocument($html);
        $xpath = new \DOMXPath($doc);
        
        
         /*
            # Regular price

            <span class="price price-before">149,00€</span>

            # Sale price

            <p class="price" itemprop="price" content="134.10">134,10€</p>

            # Sin stock

            Producto no disponible

            o.. la falta de..

            <button id="submit" type="submit" class="btn btn-add-to-cart" name="add_to_cart" value="add_to_cart"><i></i>Añadir al Carrito</button>
        */


        $sale_price    = null;
        $regular_price = null;
        $available     = null;

        // Regular price
        $tgs = $xpath->query('//span[contains(@class, "price-before")]');
        

        if (isset($tgs[0])){
            $regular_price = trim($tgs[0]->nodeValue);
        
            $regular_price = str_replace(['€', ' ', '.'], '', $regular_price);
            $regular_price = str_replace(',', '.', $regular_price);  // punto decimal
        } 
        
        // Sale price
        $tgs = $xpath->query('//p[contains(@class, "price")]');

        if (isset($tgs[0])){
            $sale_price = trim($tgs[0]->nodeValue);

            $sale_price = str_replace(['€', ' ', '.'], '', $sale_price);
            $sale_price = str_replace(',', '.', $sale_price);  // punto decimal
        } 


        // Available?
        $available = Strings::contains('btn-add-to-cart', $html);


        // Logica
        
        if ($regular_price === null && $sale_price !== null){
            $regular_price = $sale_price;
            $sale_price    = null;
        }


        // dd($regular_price, 'REG PRICE');
        // dd($sale_price, 'SALE PRICE');
        // dd($available, 'DISP?');
        

        return [
            'stock_status'  => $available,
            'regular_price' => $regular_price,
            'sale_price'    => $sale_price
        ];
    }
    
}


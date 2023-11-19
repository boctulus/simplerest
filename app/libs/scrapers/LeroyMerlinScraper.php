<?php

namespace simplerest\libs\scrapers;

use simplerest\core\Model;
use simplerest\core\libs\Dom;
use simplerest\core\libs\Url;
use simplerest\core\libs\XML;
use simplerest\core\libs\Files;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;

/*
    Ya solo es posible scrapearlo con "Selenium"

    Response: "Please enable JS and disable any ad blocker"
*/

class LeroyMerlinScraper 
{    
    static function getProduct(string $ori){
        if (Strings::startsWith('http', $ori)){
            /*
                Es una url
            */

            $url = $ori;

            $fs = explode('/', parse_url($url, PHP_URL_PATH));

            if (count($fs)<2){
                throw new \InvalidArgumentException("Url inválida: no hay slug?");
            }
            
            $slug = Url::lastSlug($url);

            if (!isset($slug)){
                throw new \InvalidArgumentException("Url inválida: no hay slug?");
            }

            $client = ApiClient::instance()
            ->followLocations()
            ->disableSSL();

            $res = $client
           
            //->cache()
            ->get($ori)
            ->getResponse(false);

            dd($res, 'RES');
            exit;

            // if ($res === null){
            //     $res = $client->disableSSL()
            //     ->followLocations()
            //     ->clearCache()
            //     ->get()
            //     ->getResponse(false);
            // }

            if ($res === null){
                return;
            }

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
        if (!Strings::contains('descriptionText', $html)){
            Logger::logError("Parsing of $ori as failed");
            return;
        }

        $doc   = XML::getDocument($html);
        $xpath = new \DOMXPath($doc);
        
        // // Title
        // $tgs  = $xpath->query('//h1');
        // $title = trim($tgs[0]->nodeValue ?? '');
        // #d($title, 'TITLE');

        // // Description (paragro dentro de section)
        // $nodelist_decr   = $xpath->query('//div[contains(@class, "descriptionText")]');

        // // como HTML
        // $desc = trim(
        //     $doc->saveHTML($nodelist_decr[0])
        // );

        // $desc = strip_tags($desc, ['strong', 'b', 'i', 'u']);
        // $desc = Strings::before($desc, 'Ver ficha');
        
        // #d($desc, 'DESC');
        
        // Precio
        $tgs = $xpath->query('//div[contains(@class, "main-price")]');
        $precio = trim($tgs[0]->nodeValue);
        $precio = Strings::first($precio, ' ');
        $precio = str_replace(['€', ' ', '.'], '', $precio);
        $precio = str_replace(',', '.', $precio);  // punto decimal
        #d($precio, 'PRECIO (NORMAL)');

        // SKU
        $sku = $xpath->query('//span[contains(@itemprop, "sku")]/@data-sku')[0]->nodeValue;
        $sku = trim($sku);

        #d($sku, 'SKU');

        // Stock status
        $tgs        = $xpath->query('//button[contains(@id, "add-to-cart")]');
        $prod_stock_status = trim($tgs[0]->nodeValue);
        $prod_stock_status = ($prod_stock_status != 'Agotado') ? 'in stock' : 'out of stock';
        #d($prod_stock_status, 'STOCK STATUS');
        
        // Caractersticas (campos varios)
        $li_nodes = $xpath->query("//ul[contains(@class, 'tablaTech')]/li/div[contains(@class, 'boxContents')]");


        /*
            [Altura espejo (en cm)] => 38
            [Altura total (en cm)] => 38
            [Anchura espejo (en cm)] => 38
            [Anchura total (en cm)] => 38
            [Color de la moldura] => Cobre
            [Acabado del producto] => Mate
            [Familia de color] => Naranja / cobre
            [Marca del producto] => CM CREATION
            [Película de protección en la parte posterior] => No
            [Peso neto (en kg)] => 1.35
            [Modelo comercial] => REDONDO BRONCE
            [Tipo de fijación] => Herrajes de suspensión suministrados Adapte el tipo de fijación en la pared, ladrillo, placa de yeso... (vendidas por separado)
            [Garantía (en años)] => 3
        */

        // $li_arr = [];
        // foreach ($li_nodes as $li_node){
        //     $li = trim($li_node->nodeValue);

        //     [$key, $val] = explode("\n", $li);
        //     $key = trim($key);
        //     $val = trim($val);

        //     $li_arr[$key] = $val;
        // }

        // // Imágenes
        // $image_nodelist = $xpath->query('//button//img/@data-lazy');
        
        // $images = [];
        // foreach($image_nodelist as $node){
        //     $images[] = $node->nodeValue;
        // }

        // $images= array_unique($images);
        #dd($images, 'IMAGES');
        
        return [
            'stock_status' => $prod_stock_status,
            'regular_price' => (float) $precio
        ];
    }
    
}



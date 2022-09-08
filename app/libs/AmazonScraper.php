<?php

namespace simplerest\libs;

use simplerest\core\libs\Dom;
use simplerest\core\libs\Url;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;

class AmazonScraper
{
    static function parseProduct(string $ori) {
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

            $client = new ApiClient($url);

            $res = $client->disableSSL()
            ->followLocations()
            ->cache()
            ->get()
            ->getResponse(false);

            if ($res === null){
                $res = $client->disableSSL()
                ->followLocations()
                ->clearCache()
                ->get()
                ->getResponse(false);
            }

            /*
                Redireccion fuera del standard de Amazon
            */

            while ($res['http_code'] == 301){
                $doc   = Dom::getDomDocument($res['data']);
                $xpath = new \DOMXPath($doc);
                
                // Url del destino
                $tgs  = $xpath->query('//a/@href');
                $url  = trim($tgs[0]->nodeValue);

                $client = new ApiClient($url);

                $res = $client->disableSSL()
                ->followLocations()
                ->clearCache()
                ->get()
                ->getResponse(false);
            }
        

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
        if (!Strings::contains('productTitle', $html)){
            Files::logger("Parsing of $ori as failed");
            return;
        }

        // ..

        $doc   = Dom::getDomDocument($html);
        $xpath = new \DOMXPath($doc);

        $tgs    = $xpath->query('//span[contains(@class, "a-price")]//span[contains(@class, "a-offscreen")]');
        $precio = trim($tgs[0]->nodeValue ?? '');

        $precio = Strings::parseCurrency($precio, '.', ',');

        /*
            DISPONIBILIDAD 

            Podria usarse el texto dentro del div con id availability pero podria estar en cualquier idioma
            y las clases de CSS podrian variar

            <div id="availability" class="a-section a-spacing-base }">               
                <span class="a-size-medium a-color-price">    No disponible.   </span>           <br>      
            </div>

            vs.

            <div id="availability" class="a-section a-spacing-base }">                 
                <span class="a-size-medium a-color-success">    En stock.   </span>           <br/>      
            </div>  

            Posibilidades:

            No disponible.
            En stock.
            Sólo queda(n) {cantidad} en stock.
        */

        $tgs  = $xpath->query('//div[@id="availability"]');
        $disp = trim($tgs[0]->nodeValue ?? '');

        return [
            'stock_status' => (bool) Strings::contains('en stock', $disp, false),
            'regular_price' => Strings::convertIntoFloat($precio)
        ];
     }


}


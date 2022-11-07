<?php

namespace simplerest\libs\scrapers;

use simplerest\core\libs\Dom;
use simplerest\core\libs\Url;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;

class AmazonScraper
{
    static function get_api_client(string $url){
        $proxy_url = "http://2.56.221.125/php-proxy/Proxy.php";
    
        return (new ApiClient($proxy_url))
        ->setHeaders([
            'Proxy-Auth: Bj5pnZEX6DkcG6Nz6AjDUT1bvcGRVhRaXDuKDX9CjsEs2',
            'Proxy-Target-URL: '.$url
        ]);
    }

    static function parseProduct(string $ori) {
        $decimal_sep   = ',';
        $thousand_sep  = '.';

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
            
            $client = static::get_api_client($url);

            $res = $client->disableSSL()
            ->followLocations()
            //->cache()
            ->get()
            ->getResponse(false);

            // if ($res === null){
            //     $res = $client->disableSSL()
            //     ->followLocations()
            //     ->clearCache()
            //     ->get()
            //     ->getResponse(false);
            // }

            /*
                Redireccion fuera del standard de Amazon
            */

            while ($res['http_code'] == 301){
                $doc   = Dom::getDomDocument($res['data']);
                $xpath = new \DOMXPath($doc);
                
                // Url del destino
                $tgs  = $xpath->query('//a/@href');
                $url  = trim($tgs[0]->nodeValue);

                $client = static::get_api_client($url);

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

        $tgs = $xpath->query('//span[contains(@class, "priceToPay")]//span//span[contains(@class, "a-price-whole")]');
        
        // No se encuentra precio => no disponible
        if ($tgs[0] === null || $tgs[0]->nodeValue === null){
            $buying_choices = Strings::contains('Ver todas las opciones de compra', $html);

            if ($buying_choices){
                dd("El producto tiene 'opciones de compra' que el usuario debe seleccionar. Imposible leer un precio");
            } else {
                // razon generica
                dd("No encuentro precio para '$url'");
            }

            return [
                'stock_status'  => 'out of stock',
                'regular_price' => 0
            ];
        }

        $node_val             = rtrim($tgs[0]->nodeValue, $decimal_sep) ?? '';
        $precio_parte_entera  = str_replace($thousand_sep, '', trim($node_val));

        $tgs = $xpath->query('//span[contains(@class, "priceToPay")]//span//span[contains(@class, "a-price-fraction")]');
        $precio_parte_decimal = trim($tgs[0]->nodeValue ?? '');

        if (empty($precio_parte_decimal)){
            $tgs = $xpath->query('//span[contains(@class, "priceToPay")]//span//span[contains(@class, "a-price-decimal")]');
            $precio_parte_decimal = trim($tgs[0]->nodeValue ?? '');
        }

        $precio = "{$precio_parte_entera}.{$precio_parte_decimal}";

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

            Tambien... al menos para productos PRIME puede haber algo como:

            <span data-csa-c-type="element" data-csa-c-content-id="DEXUnifiedCXPDM" data-csa-c-delivery-price="por 3,99&nbsp;€" data-csa-c-value-proposition="" data-csa-c-delivery-type="Entrega" data-csa-c-delivery-time="el miércoles, 7 de diciembre" data-csa-c-delivery-condition="" data-csa-c-pickup-location="" data-csa-c-distance="" data-csa-c-delivery-cutoff="" data-csa-c-mir-view="CONSOLIDATED_CX" data-csa-c-mir-type="DELIVERY" data-csa-c-mir-sub-type="" data-csa-c-mir-variant="DEFAULT" data-csa-c-delivery-benefit-program-id="rafn" data-csa-c-id="j6ywby-y2ufy-51n2vt-828mw9"> Entrega por 3,99&nbsp;€ el <span class="a-text-bold">miércoles, 7 de diciembre</span> </span>a<span
            data-csa-c-type="element"
            data-csa-c-content-id="DEXUnifiedCXPDM"
            data-csa-c-delivery-price="por 3,99&nbsp;€"
            data-csa-c-value-proposition=""
            data-csa-c-delivery-type="Entrega"
            data-csa-c-delivery-time="el miércoles, 7 de diciembre"
            data-csa-c-delivery-condition=""
            data-csa-c-pickup-location=""
            data-csa-c-distance=""
            data-csa-c-delivery-cutoff=""
            data-csa-c-mir-view="CONSOLIDATED_CX"
            data-csa-c-mir-type="DELIVERY"
            data-csa-c-mir-sub-type=""
            data-csa-c-mir-variant="DEFAULT"
            data-csa-c-delivery-benefit-program-id="rafn"
            data-csa-c-id="j6ywby-y2ufy-51n2vt-828mw9"
            >
                Entrega por 3,99&nbsp;€ el <span class="a-text-bold">miércoles, 7 de diciembre</span>
            </span>

        */

        $tgs  = $xpath->query('//div[@id="availability"]');
        $availability = trim($tgs[0]->nodeValue ?? '');

        $available             = Strings::contains('En stock',  $availability, false) || 
                                 Strings::contains('In stock',  $availability, false);

        $_delivery_date         = Strings::match($html, '/data-csa-c-delivery-time="([^"]+)/');

        if ($_delivery_date !== false){
            $available_in_a_future = (Strings::containsAnyWord([
                'lunes',
                'martes',
                'miércoles',
                'jueves',
                'viernes',
                'sábado',
                'domingo'
            ], $_delivery_date));                  
        } else {
            $available_in_a_future = null;
        }          

        $available_soon = Strings::contains('Envío en ', $availability);

        return [
            'stock_status'  => ($available || $available_soon || $available_in_a_future) ? 'in stock' : 'out of stock',
            'regular_price' => Strings::convertIntoFloat($precio)
        ];
     }
}


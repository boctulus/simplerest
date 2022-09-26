<?php

namespace simplerest\libs;

use simplerest\core\libs\Dom;
use simplerest\core\libs\Url;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;

class MaisonsScraper 
{    
    static function parseProduct(string $ori){
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

            $html = ApiClient::instance($url)
            ->disableSSL()
            ->cache()
            ->get()
            ->getResponse(false)['data'];
            
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
        if (!Strings::contains('description-link', $html)){
            Files::logger("Parsing of $ori as failed");
            return;
        }

        $doc   = Dom::getDomDocument($html);
        $xpath = new \DOMXPath($doc);
        
        // // Title
        // $tgs  = $xpath->query('//h1');
        // $title = trim($tgs[0]->nodeValue);
        // #d($title, 'TITLE');

        // // Description (paragro dentro de section)
        // $nodelist_decr   = $xpath->query('//section[contains(@class, "description-block")]//p');

        // // como HTML
        // $desc = trim(
        //     $doc->saveHTML($nodelist_decr[0])
        // );
        
        #d($desc, 'DESC');
        
        // Precio
        $tgs = $xpath->query('//strong[contains(@class, "base-price")]');
        $precio = trim($tgs[0]->nodeValue);    
        $precio = str_replace(['€', ' ', ' ', '.'], '', $precio);    
        $precio = str_replace(',', '.', $precio);  // punto decimal
        #d($precio, 'PRECIO (NORMAL)');
            
        // // Costo recogida
        // $str = 'Entrega a domicilio o a un punto de recogida:';

        // /*
        //     Nodo que contiene cierto string
        //     https://stackoverflow.com/a/3726386/980631
        // */
        // $tgs   = $xpath->query("//*[text()[contains(., '$str')]]");
        // $node  = $tgs[0]->parentNode;

        // $costo_entrega = $node->nodeValue;
        // $costo_entrega = trim( Strings::after($costo_entrega, ':') );
        // $costo_entrega = str_replace(['€', ' ', '.'], '', $costo_entrega);
        // $costo_entrega = str_replace(',', '.', $costo_entrega);  // punto decimal
        // #d($costo_entrega, 'Costo entrega');

        // Stock status
        $tgs        = $xpath->query('//span[contains(@class, "product-stock")]');
        $prod_stock_status = trim($tgs[0]->nodeValue ?? '');
        $prod_stock_status = ($prod_stock_status === 'En stock' || $prod_stock_status === 'Disponible') ? 'in stock' : 'out of stock';
        #d($prod_stock_status, 'STOCK STATUS');
        
        // Caractersticas (campos varios)
        $li_nodes = $xpath->query("//h3[text()[contains(., 'Características')]]/..//ul/li");

        /*
            Ref.: 121734
            Dimensiones (cm):  Alto75 x Ancho153 x Profundo13
            Peso (kg): 26
            Made In Indonesie
            Origen de la madera principal: Indonesia
            Material principal: Espejo
            Tipo de madera principal: Teca
            Nombre científico de la madera principal: Tectona Grandis l.f.
            Material principal: Madera
        */

        // $materials = [];

        // $li_arr = [];
        // foreach ($li_nodes as $li_node){
        //     $li = trim($li_node->nodeValue);

        //     if (Strings::startsWith('Ref.:', $li)){
        //         $sku = trim(
        //             Strings::after($li, ':')
        //         );
        //         #d($sku, 'sku');
        //     }

        //     if (Strings::startsWith('Dimensiones (cm):', $li)){
        //         $dimensions = trim(
        //             Strings::after($li, ':')
        //         );

        //         $dimensions = explode('x', $dimensions);

        //         array_walk($dimensions, function(&$item){
        //             $item = Strings::match($item, '/([0-9]+)/');
        //         });
                
        //         #d($dimensions, 'dimensions');
        //     }

        //     if (Strings::startsWith('Peso (kg):', $li)){
        //         $weight = trim(
        //             Strings::after($li, ':')
        //         );
        //         #d($weight, 'weight');
        //     }

        //     if (Strings::startsWith('Made In ', $li, false)){
        //         $origin = trim(
        //             Strings::after($li, 'In ')
        //         );
        //         #d($origin, 'origin');
        //     }

        //     if (Strings::startsWith('Material principal:', $li, false)){
        //         $_material = trim(
        //             Strings::after($li, ':')
        //         );

        //         $materials[] = strtolower($_material);
        //     }

        //     $li_arr[] = $li;
        // }


        // $materials = implode(", ", $materials);        
        // #d($materials, 'Materials');

        // $caracteristicas = implode("<br/>".PHP_EOL, $li_arr);
        // #d($caracteristicas, 'Caracteristicas');

        // // Imágenes
        // $image_nodelist = $xpath->query('//div[contains(@class, "ratio-image-child")]//img/@data-src');
        
        // $images = [];
        // foreach($image_nodelist as $node){
        //     $images[] = $node->nodeValue;
        // }

        // $images= array_unique($images);
        #dd($images);

        return [
            'stock_status' => $prod_stock_status,
            'regular_price' => (float) $precio
        ];
    }


}

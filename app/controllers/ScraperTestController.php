<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyController;

class ScraperTestController extends MyController
{
    private static function getDetailField(string $details, string $fieldName): string
    {
        $pattern = '/' . preg_quote($fieldName, '/') . '\s*([^<]+)/i';
        if (preg_match($pattern, $details, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }

    function scrapeProductDetails($html)
    {
        $dom = new \DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);

        // Array para almacenar los datos extraídos
        $productDetails = array();

        // Extraer nombre del producto
        $productNameNode = $xpath->query('//h1[@class=""]')->item(0);
        if ($productNameNode) {
            $productDetails['product_name'] = trim($productNameNode->nodeValue);
        }

        // Extraer precio del producto
        $priceNode = $xpath->query('//b[@class="price salePrice"]')->item(0);
        if ($priceNode) {
            $productDetails['price'] = trim($priceNode->nodeValue);
        }

        // Extraer descuento
        $discountNode = $xpath->query('//i')->item(0);
        if ($discountNode) {
            $productDetails['discount'] = trim($discountNode->nodeValue);
        }

        // Extraer opciones de talla
        $sizeOptions = $xpath->query('//select[@id="sizeSelect"]/optgroup/option');
        foreach ($sizeOptions as $sizeOption) {
            $value = $sizeOption->getAttribute('value');
            $price = $sizeOption->getAttribute('data-price');
            $productDetails['size_options'][$value] = array(
                'price' => $price,
                'label' => trim($sizeOption->nodeValue),
            );
        }

        // Extraer descripción del producto
        $descriptionNode = $xpath->query('//section[@class="product_editorial"]//p')->item(0);
        if ($descriptionNode) {
            $productDetails['description'] = trim($descriptionNode->nodeValue);
        }

        // Obtener la imagen destacada
        $featuredImageNode = $xpath->query('//div[contains(@class, "prod-slider__slide--active")]//img')->item(0);
        $featuredImageUrl = $featuredImageNode ? $featuredImageNode->getAttribute('src') : '';

        // Obtener las imágenes adicionales
        $additionalImages = [];
        $imageNodes = $xpath->query('.//div[contains(@class, "prod-carousel__slide")]//img');
        foreach ($imageNodes as $imageNode) {
            $additionalImages[] = $imageNode->getAttribute('src');
        }

        $productDetails['featured_img'] = $featuredImageUrl;
        $productDetails['images'] = $additionalImages;
            
        // Obtener descripción del producto
        $descriptionNode = $xpath->query('//section[contains(@class, "product_editorial")]//p')->item(0);
        $productDetails['descrizione'] = $descriptionNode ? trim($descriptionNode->nodeValue) : '';

        // // Obtener el bloque de detalles del producto
        $productDetailsNode = $xpath->query('//span[contains(@class, "product__details")]')->item(0);
        $productDetailsText = $productDetailsNode ? $productDetailsNode->nodeValue : '';

        // Extraer campos específicos del bloque de detalles del producto
        $designerCode = self::getDetailField($productDetailsText, 'Designer code:');
        $designerColorCode = self::getDetailField($productDetailsText, 'Designer color code:');
        $giglioCode = self::getDetailField($productDetailsText, 'Giglio Code:');

        $productDetails['designer_code'] = $designerCode;
        $productDetails['designer_color_code'] = $designerColorCode;
        $productDetails['giglio_code'] = $giglioCode;

        return $productDetails;
    }

    /*
        php com dumb scraper_demo url='https://www.giglio.com/scarpe-uomo_sneakers-alexander-mcqueen-586198whx52.html?cSel=002'
    */

    function run(){
        $prod_url = $_GET['url']; // 'https://www.giglio.com/scarpe-uomo_sneakers-alexander-mcqueen-586198whx52.html?cSel=002'
 
        $cli = (new ApiClient($prod_url))
        ->withoutStrictSSL()
        ->setHeaders([
            'User-Agent' => 'PostmanRuntime/7.34.0',
        ])
        ->cache(600000);

        $cli->setMethod('GET');

        $cli->send();

        $res = $cli->data();
        
        // dd($res);

        dd($this->scrapeProductDetails($res));
    }

}


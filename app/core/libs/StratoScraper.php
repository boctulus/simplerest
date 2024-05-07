<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\ProductScraper;

/*
    strato.es
*/
class StratoScraper extends ProductScraper
{
    public static function getProduct(string $slug){
        $html = static::getHTML($slug);

        preg_match_all('/data-src-l="([^"]+)"/', $html, $matches);
        $image_urls = $matches[1];

        $crawler = new DomCrawler($html);

        // Extraer información del producto
        $productName = $crawler->getText('h1[itemprop="name"]');
        $itemCondition = $crawler->getAttr('meta[itemprop="itemCondition"]','content');
        $sku = $crawler->getAttr('meta[itemprop="sku"]','content');
        $manufacturer = $crawler->getAttr('meta[itemprop="manufacturer"]','content');
        $brand = $crawler->getAttr('meta[itemprop="brand"]','content');
        $category = $crawler->getAttr('meta[itemprop="category"]','content');
        $description = $crawler->getText('.description[itemprop="description"]');

        // Imprimir la información del producto
        $productData = [
            'name' => $productName,
            'itemCondition' => $itemCondition,
            'sku' => $sku,
            'manufacturer' => $manufacturer,
            'brand' => $brand,
            'category' => $category,
            'description' => $description,
            'images'  => $image_urls
        ];

        return $productData;
    }


}


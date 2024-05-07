<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\ProductScraper;

class StatoScraper extends ProductScraper
{
    public static function getProduct(string $slug){
        $html = static::getHTML($slug);

        preg_match_all('/data-src-l="([^"]+)"/', $html, $matches);
        $image_urls = $matches[1];

        $crawler = new DomCrawler($html);

        // Extraer información del producto
        $productName = $crawler->get('h1[itemprop="name"]');
        $itemCondition = $crawler->get('meta[itemprop="itemCondition"]','content');
        $sku = $crawler->get('meta[itemprop="sku"]','content');
        $manufacturer = $crawler->get('meta[itemprop="manufacturer"]','content');
        $brand = $crawler->get('meta[itemprop="brand"]','content');
        $category = $crawler->get('meta[itemprop="category"]','content');
        $description = $crawler->get('.description[itemprop="description"]');

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


<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\ProductScraper;

/*
    strato.es
*/
class StratoScraper extends ProductScraper
{
    protected static function getUrlSlug($url)
    {
        // Obtener la parte de la URL después del último "/"
        $parts = explode('/', $url);
        $lastPart = end($parts);
        
        // Decodificar la URL
        $decodedUrl = urldecode($lastPart);
        
        // Reemplazar caracteres especiales con "-"
        $slug = preg_replace('/[^a-zA-Z0-9]+/', '-', $decodedUrl);
        
        // Convertir a minúsculas
        $slug = strtolower($slug);
        
        return $slug;
    }

    protected static function transformCategories($categories, $parentSlug = '')
    {
        global $transformedCategories;
        
        foreach ($categories as $category) {
            // Obtener el slug de la URL
            $slug = static::getUrlSlug($category['url']);
            
            // Crear un array con la estructura deseada
            $transformedCategory = [
                'name' => $category['name'],
                'slug' => $slug,
                'parent_slug' => $parentSlug,
            ];
            
            // Agregar la categoría transformada al array resultante
            $transformedCategories[] = $transformedCategory;
            
            // Si la categoría tiene subcategorías, llamar recursivamente a esta función
            if (!empty($category['subcategories'])) {
                static::transformCategories($category['subcategories'], $slug);
            }
        }
    }

    public static function getCategoList(string $html){
        $crawler = new DomCrawler($html);

        $categories = [];

        // Encontrar todas las categorías principales
        $categoryElements = $crawler->filter('.ep-megamenu-first-level-element');

        // Recorrer cada elemento de categoría principal
        $categoryElements->each(function ($categoryElement) use (&$categories) {
            $categoryName = $categoryElement->filter('a')->text();
            $categoryUrl = $categoryElement->filter('a')->attr('href');

            // Array temporal para almacenar las subcategorías
            $subcategories = [];

            // Encontrar todas las subcategorías
            $subcategoryElements = $categoryElement->filter('.ep-megamenu-second-level-element');

            // Recorrer cada elemento de subcategoría
            $subcategoryElements->each(function ($subcategoryElement) use (&$subcategories) {
                $subcategoryName = $subcategoryElement->filter('a')->text();
                $subcategoryUrl = $subcategoryElement->filter('a')->attr('href');

                // Almacenar la subcategoría en el array temporal
                $subcategories[] = [
                    'name' => $subcategoryName,
                    'url' => $subcategoryUrl,
                ];
            });

            // Almacenar la categoría y sus subcategorías en el array principal
            $categories[] = [
                'name' => $categoryName,
                'url' => $categoryUrl,
                'subcategories' => $subcategories,
            ];
        });

        global $transformedCategories;

        static::transformCategories($categories);

        return $transformedCategories;
    }

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


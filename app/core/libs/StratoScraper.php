<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\ProductScraper;

/*
    strato.es
*/
class StratoScraper extends ProductScraper
{

    /*
        Obtiene paginador de pagina de productos (u otras)

        TO-DO

        - Generar salida basada en Interfaz y validaciones (opcional)
    */
    public static function getPaginator($html){
        $crawler = new DomCrawler($html);
    
        // Encontrar el elemento que contiene la información del paginador
        $paginatorElement = $crawler->filter('.PagedProduct .PagerTable');
    
        // Obtener el texto que contiene la información de resultados
        $paginatorInfoText = $paginatorElement->filter('.PagerInfoLeft')->text();
    
        // Utilizar expresiones regulares para extraer la información necesaria
        $matches = [];
        preg_match('/(\d+) - (\d+) de (\d+) resultados/', $paginatorInfoText, $matches);
    
        // Extraer la cantidad de resultados, la cantidad de páginas y la página actual
        $startIndex = intval($matches[1]);
        $endIndex = intval($matches[2]);
        $totalResults = intval($matches[3]);
        $pageSize = $endIndex - $startIndex + 1;
        $totalPages = ceil($totalResults / $pageSize);
    
        // Determinar la página actual
        $currentPage = 1;
        $currentPageElement = $paginatorElement->filter('.PagerSizeContainer li.Current');
        if ($currentPageElement->count() > 0) {
            $currentPage = intval($currentPageElement->text());
        }

    
        // Retornar un array con la información del paginador
        return [
            'totalResults' => $totalResults,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ];
    }
    

    protected static function getUrlSlug($url)
    {
        // Decodificar la URL
        $decodedUrl = urldecode($url);

        $decodedUrl = Strings::lastSegmentOrFail($decodedUrl, '/');
        
        // Convertir caracteres a su equivalente ASCII
        $asciiUrl = Strings::accents2Ascii($decodedUrl);
        
        // Reemplazar caracteres especiales con "-"
        $slug = preg_replace('/[^a-zA-Z0-9\/]+/', '-', $asciiUrl);
        
        // Convertir a minúsculas
        $slug = trim(strtolower($slug), '-');
        
        return $slug;
    }

    /*
        TO-DO

        - Generar salida basada en Interfaz y validaciones (opcional)
    */
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
                'url' => $category['url'], // Conservar la URL original
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

    public static function getCatego(string $html)
    {
        $crawler = new DomCrawler($html);
        
        // Encuentra el elemento que contiene la información de la categoría
        $categoryElement = $crawler->filter('.CategoryList');

        // Extrae el nombre de la categoría
        $categoryName = $categoryElement->filter('.CategoryText i b')->text();
        
        // Extrae la descripción de la categoría
        $categoryDescription = $categoryElement->filter('.CategoryText')->html();

        // Encuentra todas las imágenes de la categoría
        $imageElements = $categoryElement->filter('.CategoryImage img');
        $images = [];

        // Itera sobre cada imagen y obtén su URL
        $imageElements->each(function ($imageElement) use (&$images) {
            $imageUrl = $imageElement->attr('src');
            $imageUrl = Url::toAbsolute($imageUrl);

            $images[] = $imageUrl;
        });

        // Almacena la información de la categoría en un array
        $categoryData = [
            'name' => $categoryName,
            'description' => $categoryDescription,
            'images' => $images,
        ];

        return $categoryData;
    }

    /*
        TO-DO

        - Completar separando atributos
        - Generar salida basada en Interfaz y validaciones (opcional)
    */
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
        $description = $crawler->getHTML('.description[itemprop="description"]');

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


<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\ProductScraper;

/*
    strato.es

    ✅ Lista de categorias de productos                 getCategoList
    ✅ Paginador de categorias                          getPaginator
    ✅ Pagina de categoria                              getCategoryPageURL, getCategoryPage
    ✅ Links de productos dentro de pagina de cat.      getProductLinks
    ✅ Atributos prentes en variaciones                 __getVariantAttrs
    ✅ Pagina de producto (faltan variaciones)          getProduct

    getCategoList -> getPaginator -> getCategoryPageURL -> getProductLink -> getProduct
*/
class StratoScraper extends ProductScraper
{
    protected static $objectID;

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
        $startIndex   = intval($matches[1]);
        $endIndex     = intval($matches[2]);
        $totalResults = intval($matches[3]);
        $pageSize     = $endIndex - $startIndex + 1;
        $totalPages   = ceil($totalResults / $pageSize);
    
        // Determinar la página actual
        $currentPage = 1;
        $currentPageElement = $paginatorElement->filter('.PagerSizeContainer li.Current');
        if ($currentPageElement->count() > 0) {
            $currentPage = intval($currentPageElement->text());
        }

        // Retornar un array con la información del paginador
        return [
            'totalResults' => $totalResults,
            'totalPages'   => $totalPages,
            'currentPage'  => $currentPage
        ];
    }
    
    /*
        Obtiene URL de pagina de categoria paginada o sea la parametriza a fin de paginarla
    */
    public static function getCategoryPageURL(string $category_url, int $page, $page_size = null)
    {
        if ($page_size === null){
            $page_size = 50;
        }

        $html      = static::getHTML($category_url);

        $object_id = Strings::matchOrFail($html, "/objectId: '(\d+)'/");

        return static::$urlBase . "?ViewAction=View&ObjectID=$object_id&PageSize=$page_size&Page=$page";
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
    
    public static function getProductLinks(string $html)
    {
        $crawler = new DomCrawler($html);

        // Seleccionar la sección HotDealList
        $hotDealList = $crawler->filter('div.HotDealList');

        // Array para almacenar los enlaces de productos
        $productLinks = [];

        // Verificar si se encontró la sección HotDealList
        if ($hotDealList->count() > 0) {
            // Buscar todos los enlaces dentro de la sección HotDealList
            $hotDealList->filter('div.HotDeal')->each(function ($hotDeal) use (&$productLinks) {
                // Obtener el enlace del producto
                $productLink = $hotDeal->filter('a.ProductName')->attr('href');
                // Agregar el enlace al array de enlaces de productos
                $productLinks[] = $productLink;
            });
        }

        // Devolver el array de enlaces de productos
        return $productLinks;
    }


    /*
        Duvuelve "New" por ej.
    */
    protected static function extractCondition($url) {
        // Define the regex pattern
        $pattern = '|schema\.org/([^/]+)|';
        
        // Perform the match
        return Strings::matchOrFail($url, $pattern);
    }

    /*
        @return array slugs de productos que son accesorios
    */
    public static function extractAccessories($html) : array {    
        // Crear un objeto Crawler para analizar el HTML
        $crawler = new DomCrawler($html);
    
        // Seleccionar la tabla con la clase "VariationsTable"
        $container = $crawler->filter('table.VariationsTable');
    
        // Verificar si se encontró la tabla
        if ($container->count() > 0) {
            // Imprimir el HTML de la tabla
            $containerContent = $container->html();
        } else {
            return [];
        }

        $p_slugs = Strings::matchAll($containerContent, '/href="\?ObjectPath=\/Shops\/[0-9]+\/Products\/([a-z0-9%]+)">/i');

        return $p_slugs;
    }

    /*
        @return array slugs de productos relacionados
    */
    public static function extractRelatedProdducts($html) : array {
        $arr = [];   

        // Crear un objeto Crawler para analizar el HTML
        $crawler = new DomCrawler($html);
    
        $crawler->filter('div.CrossellingImageArea')->each(function ($container) use (&$arr) {
              // Verificar si se encontró la tabla
            if ($container->count() > 0) {
                // Imprimir el HTML de la tabla
                $content = $container->html();

                $slug = Strings::match($content, '|Shops/[0-9]+/Products/([a-z0-9%]+)|i');

                $arr[] = $slug;
            } 
        });          
     

        return $arr;
    }

    static function __getVariantAttrs($html, $ucfirst = false)
    {
        /*
            Devuelve atributos de un producto variable

            Es compatible con la interfaz de Giglio

            Array
            (
                [Medida] => Array
                    (
                        [0] => Array
                            (
                                [0] => 60x45x60
                                [1] => 125988553
                            )

                        [1] => Array
                            (
                                [0] => 80x45x60
                                [1] => 125988558
                            )

                        [2] => Array
                            (
                                [0] => 100x45x60
                                [1] => 125988563
                            )

                        [3] => Array
                            (
                                [0] => 120x45x60 Dos muebles.
                                [1] => 125988568
                            )

                    )

            )
        */

        $variations = [];
    
        // Crear un objeto Crawler para analizar el HTML
        $crawler = new DomCrawler($html);
    
        // Seleccionar todas las tablas con la clase "SelectVariation"
        $crawler->filter('table.SelectVariation')->each(function ($table) use (&$variations, &$ucfirst) {
            // Recuperar todos los <th> con sus correspondientes 'for' y textos
            $headers = [];
            $table->filter('th')->each(function ($th) use (&$headers) {
                $label = $th->filter('label');
                if ($label->count() > 0) {
                    $for = $label->attr('for');
                    $text = trim($label->text());
                    $headers[$for] = $text;
                }
            });
    
            // Recorrer todos los <td> y verificar los <select>
            $table->filter('td')->each(function ($td) use (&$headers, &$variations, &$ucfirst) {
                $select = $td->filter('select');
                if ($select->count() > 0) {
                    $id = $select->attr('id');
                    if (isset($headers[$id])) {
                        $variationName = $headers[$id];
                        $options = [];
                        $select->filter('option')->each(function ($option) use (&$options, &$ucfirst) {
                            $value = $option->attr('value');
                            $text = trim($option->text());

                            if ($ucfirst && !is_numeric($value)){
                                $value = ucfirst(strtolower($value));
                            }

                            if (!empty($value)){
                                $options[] = [
                                    $text,
                                    $value
                                ];
                            }
                        });

                        $variations[$variationName] = $options;
                    }
                }
            });
        });
    
        return $variations;
    }

    /*
        TO-DO

        - Completar separando atributos
        - Generar salida basada en Interfaz y validaciones (opcional)
    */
    public static function getProduct(string $slug){
        $html = static::getHTML($slug);   
        
        dd(static::__getVariantAttrs($html)); exit; ///

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

        // Obtener el contenedor de precios
        $priceContainer = $crawler->filter('.PriceContainer');

        // Obtener el precio regular (regular price)
        $regularPrice = $priceContainer->filter('.AlignLeft')->text();
        $regularPrice = preg_replace('/¡OFERTA!, antes: | €/', '', $regularPrice);

        // Obtener el precio de oferta (sale price)
        $salePrice = $priceContainer->filter('.Price .price-value')->text();

        // Obtener el porcentaje de descuento, si está disponible
        $discountPercentage = $crawler->filter('.HotPrice')->text();

        /*
            Estado de stock y cuando se envia (en t-dias)
        */

        $ps = Strings::matchAll($html, "/<p>([\s\S]*?)<\/p>/");

        $stock_status   = "outofstock"; // aunque siempre envian
        $available_in_t = null;

        foreach ($ps as $p){    
            if (Strings::contains("En existencias", $p)){
                $stock_status = "instock";
            }

            $available_in_t = Strings::match($p, "/se puede enviar en ([0-9-])/");
        }

        /*
            Post-procesamientos
        */

        $itemCondition = static::extractCondition($itemCondition);
        $regularPrice  = Strings::parseFloatOrFail($regularPrice);
        $salePrice     = Strings::parseFloatOrFail($salePrice);

        // Imprimir la información del producto
        $productData = [
            'name' => $productName,
            'itemCondition' => $itemCondition,
            'sku' => $sku,
            'manufacturer' => $manufacturer,
            'brand' => $brand,
            'category' => $category,
            'description' => $description,
            'images'  => $image_urls,

            'regularPrice' => $regularPrice,
            'salePrice' => $salePrice,
            'discountPercentage' => $discountPercentage,

            "accesories" => static::extractAccessories($html),
            "related"    => static::extractRelatedProdducts($html),

            'stock_status' => $stock_status,
            'in_t_days'    => $available_in_t
        ];

        return $productData;
    }



}


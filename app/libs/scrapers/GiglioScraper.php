<?php

namespace simplerest\libs\scrapers;

use simplerest\core\libs\JsonLd;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\core\libs\ProductScraper;

class GiglioScraper extends ProductScraper
{
    function __construct() { }

    protected static $urlBase  = 'https://www.giglio.com';
    protected static $exp_time = 21600;

    static function setExpTime($exp_time) {
        self::$exp_time = $exp_time;
    }

    ///////////////////////////// PRODUCTO ///////////////////////

    static function getProductBasicAttr($html){
        $dom = new \DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        // Crear un objeto DOMXPath para realizar consultas XPath en el documento
        $xpath = new \DOMXPath($dom);

        // Array para almacenar los datos extraídos
        $productDetails = array();

        // Extraer nombre del producto
        $productNameNode = $xpath->query('//h1[@class=""]')->item(0);
        if ($productNameNode) {
            $productDetails['product_name'] = trim($productNameNode->nodeValue);
        }

        // Extraer descripción del producto
        $descriptionNode = $xpath->query('//section[contains(@class, "product_editorial")]//p')->item(0);
        if ($descriptionNode) {
            $productDetails['desc'] = trim($descriptionNode->nodeValue);
        }

        if (Strings::contains('•', $productDetails['desc'])){
            $desc_items = Strings::lines($productDetails['desc'], true, true, '•');
        }

        $desc_items = array_map(function($item) {
            return "<li>$item</li>\r\n";
        }, $desc_items);
      
        // debe ser para cada elemento. array_walk ??
        $productDetails['desc'] = "<ol>\r\n". implode('', $desc_items) . "</ol>\r\n";
    
        return $productDetails;
    }

    /*
        Obtiene categoria de producto indidual

        Hay muchas inconcistncias con el slug que a veces se halla dentro de un enlace y otras veces dentro de un span asi que no se considera
    */
    static protected function getCatego($html){
        $dom = new \DOMDocument;
        
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
    
        // Encuentra el último elemento 'li' dentro de la lista de migas de pan
        $lastLiElement = $xpath->query('//ol[@class="breadcrumbs"]/li[last()]')->item(0);
    
        if ($lastLiElement) {
            // Limpiar el contenido del nodo de texto dentro de 'span'
            $name = trim($xpath->evaluate('string(./span/text())', $lastLiElement));

            // $linkElements = $xpath->query('//ol[@class="breadcrumbs"]/li/a[@itemprop="item"]', $lastLiElement);

            // if ($linkElements->length > 0) {
            //     // Obtener el valor del atributo 'href' del último enlace
            //     $lastLinkElement = $linkElements[$linkElements->length - 1];
            //     $href = $lastLinkElement->getAttribute('href');

            //     // Formar el slug eliminando la parte inicial de la URL
            //     $slug = preg_replace('#https://www\.giglio\.com#', '', $href);
            // }
            
            // $result = [
            //     // 'slug' => $slug,
            //     'name' => $name,
            // ];
    
            return $name;
        }
    
        return null; // Devolver null si no se encuentra el elemento
    }    

    static function getProduct($url)
    {
        $prod = [];

        $html = static::getHTML($url, static::$exp_time);

        $data = JsonLd::extract($html);

        if (empty($data)){
            die('No hay JSON-LD ?'); // mejor seria solo generar un log excepto en modo debug
        }

        /*
            Armo la respuesta
        */

        // nombre de producto,..
        $prod = static::getProductBasicAttr($html);

        $prod['availability'] = str_replace('https://schema.org/', '', $data[0]['offers']['availability']) ?? null;   

        $prod['attr'] = [];
        $prod['attr']['brand']    = $data[0]['brand']['name'] ?? null;  
        $prod['attr']['currency'] = $data[0]['offers']['priceCurrency'] ?? null;  
        $prod['attr']['price_until'] = $data[0]['offers']['priceValidUntil'] ?? null;   
        $prod['attr']['condition'] = str_replace('https://schema.org/', '', $data[0]['offers']['itemCondition']) ?? null;   
        $prod['attr']['seller'] = $data[0]['offers']['seller']['name'] ?? null; 

        // procesamientos adicionales 
        $prod['attr']['condition'] = str_replace('Condition', '', $prod['attr']['condition']);

        //variations
        $prod['vs'] = [];

        foreach ($data as $p){
            // variation

            $prod['vs'][] = [                
                'name'         => $p['name'] ?? null,
                'sku'          => $p['sku'] ?? null,
                'description'  => $p['description'] ?? null,
                'images'       => $p['image'] ?? null,
                'price'        => $p['offers']['price'] ?? null,
                
                'attr'         => [
                    'mpn'   => preg_replace('/[^a-zA-Z0-9.\-_]/', '', $p['mpn']) ?? '',
                    'color' => $p['color'] ?? '',
                ]
            ];
        }
        

        return $prod;                 
    }

    ////////////////////// CATEGORIES ////////////////////////

    /* 
        Categorias de primer nivel

        <ul class="header__nav__main">
        <li><a data-menu="0" class="header__nav__main__wom" href="/donna/">Donna</a></li>
        <li><a data-menu="0" class="header__nav__main__man sel gender--sel" href="/uomo/">Uomo</a></li>
        <li><a data-menu="0" class="header__nav__main__kid" href="/bambino/">Bambini</a></li>
        <li><a data-menu="0" class="header__nav__main__lif" href="/lifestyle/">Lifestyle</a></li>
        </ul>
    */
    static protected function getFirstLevelCategos($html){
        $dom = new \DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Busca los slugs de las categorías de primer nivel
        $slugs = $xpath->query('//ul[@class="header__nav__main"]/li/a/@href');

        $result = [];

        // Itera sobre los nodos de los slugs y los agrega al resultado
        foreach ($slugs as $slug) {
            $result[] = $slug->nodeValue;
        }

        return $result;
    }
    
    static function getCategos($html){
        $dom = new \DOMDocument;
    
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
    
        $categoryElements = $xpath->query('//li[contains(translate(span/text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "categoria") or contains(translate(span/text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "categoria")]');
    
        $categos = [];
    
        foreach ($categoryElements as $categoryElement) {
            $categoryName = $xpath->evaluate('string(span)', $categoryElement);
            $categoryElements = $xpath->query('.//li[.//a]', $categoryElement);
    
            if ($categoryElements->length > 0) {
                foreach ($categoryElements as $categoryElement) {
                    $categoryName = $xpath->evaluate('string(a)', $categoryElement);
                    $categorySlug = $categoryElement->getElementsByTagName('a')->item(0)->getAttribute('href');

                    $categoryName = trim($categoryName);
    
                    $categos[] = [
                        'slug' => $categorySlug,
                        'name' => $categoryName
                    ];
                }
            } else {
                // No hay categorías, solo la categoría principal
                $categorySlug = $categoryElement->getElementsByTagName('a')->item(0)->getAttribute('href');

                if (empty($categorySlug)){
                    continue;
                }
                
                $categoryName = trim($categoryName);

                $categos[] = [
                    'slug' => $categorySlug,
                    'name' => $categoryName
                ];
            }
        }

         // data processing

         foreach ($categos as $ix => $cat) {
            $cat['name'] = trim($cat['name']);

            // por alguna razon a veces falla y lo deja pasar
            if (Strings::contains('Vedi tutt', $cat['name'])){
                $categos[$ix] = null;
            }

        }
    
        return $categos;
    }

    /*
        No guardar estas categorias pero usarlas para recuperar productos

        Luego se puede obtener la categoria dentro de cada pagina de producto
    */
    static function getBrands($html){
        $dom = new \DOMDocument;
    
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
    
        $brandElements = $xpath->query('//li[contains(translate(span/text(), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "designer")]');
    
        $brands = [];
    
        foreach ($brandElements as $brandElement) {
            $name = $xpath->evaluate('string(span)', $brandElement);
            $categoryElements = $xpath->query('.//li[.//a]', $brandElement);
    
            if ($categoryElements->length > 0) {
                foreach ($categoryElements as $categoryElement) {
                    $name = $xpath->evaluate('string(a)', $categoryElement);
                    $slug = $categoryElement->getElementsByTagName('a')->item(0)->getAttribute('href');

                    if (empty($slug)){
                        continue;
                    }

                    $name = trim($name);
    
                    $brands[] = [
                        'slug' => $slug,
                        'name' => $name
                    ];
                }
            } else {
                // No hay subcategorías, solo la categoría principal
                $slug = $brandElement->getElementsByTagName('a')->item(0)->getAttribute('href');

                if (empty($slug)){
                    continue;
                }

                $name = trim($name);

                $brands[] = [
                    'slug' => $slug,
                    'name' => $name
                ];
            }
        }
    
        // data processing

        foreach ($brands as $ix => $brand) {
            if (empty($brand) || !is_array($brand) || !array_key_exists('name', $brand)) {
                unset($brands[ $ix ]);
            }

            $brand['name'] = trim($brand['name']);            
            $brand['name'] = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $brand['name']);

            // por alguna razon a veces falla y lo deja pasar
            if (Strings::contains('Vedi tutt', $brand['name'], false)){
                $brands[$ix] = null;
            }

        }

        return $brands;
    }    

    static function getCategosList(string $html) {
        $main_categos = static::getFirstLevelCategos($html);

        $main_categos_ay = [];
        foreach ($main_categos as $slug) {
            $main_categos_ay[] = [
                'slug' => $slug,
                'name'=> ucfirst(str_replace('/', '', $slug)),
            ];
        }

        $arr = $main_categos_ay;
        foreach ($main_categos as $main_cat) {
            $url  = static::$urlBase . $main_cat;

            // obtengo el html de las categorias principales
            $html = static::getHTML($url, static::$exp_time);

            $arr  = array_merge($arr, static::getCategos($html));
        }

        return $arr;
    }

    /*
        Similar en formato a getCategosList() pero no deberian insertarse 
        sino usarse de forma temporal para recuperar mas productos

        Usar getCategosList() primero y luego getBrandList()

        A los productos no se les debe cambiar el titulo ni categoria ni descripcion
        una vez asignados
    */
    static function getBrandList(string $html) {
        $main_categos = static::getFirstLevelCategos($html);

        $main_categos_ay = [];
        foreach ($main_categos as $slug) {
            $main_categos_ay[] = [
                'slug' => $slug,
                'name'=> ucfirst(str_replace('/', '', $slug)),
            ];
        }

        $arr = $main_categos_ay;
        foreach ($main_categos as $main_cat) {
            $url  = static::$urlBase . $main_cat;

            // obtengo el html de las categorias principales
            $html = static::getHTML($url, static::$exp_time);

            $arr  = array_merge($arr, static::getBrands($html));
        }

        return $arr;
    }

    ///////////////// PAGINAS CON PRODUCTOS ////////////////////

    // protected
    static function getProductLinks($html) {
        $productLinks = array();

        // Crea un nuevo objeto DOMDocument
        $dom = new \DOMDocument;

        // Carga el HTML en el objeto DOMDocument
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        // Crea un objeto DOMXPath para consultar el documento HTML
        $xpath = new \DOMXPath($dom);

        // Encuentra todos los elementos <a> con el atributo itemprop="url"
        $elements = $xpath->query('//div[@class="products-grid__el"]//a[@itemprop="url"]');

        // Recorre los elementos encontrados y obtén el valor del atributo href
        foreach ($elements as $element) {
            $productLinks[] = $element->getAttribute('href');
        }

        return $productLinks;
    }
  
}


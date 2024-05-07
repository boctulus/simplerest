<?php

namespace simplerest\libs\scrapers;

use simplerest\core\libs\JsonLd;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\core\libs\ProductScraper;
use simplerest\core\libs\Url;

class GiglioScraper extends ProductScraper
{
    function __construct() { }

    protected static $urlBase  = 'https://www.giglio.com';
    protected static $exp_time = 21600;

    
    static function getUrl() {
        return rtrim(static::$urlBase, '/');
    }

    static function getHomePage() {
        $url  = static::getUrl();
        $html = static::getHTML($url);

        return $html;
    }

    static function setExpTime($exp_time) {
        self::$exp_time = $exp_time;
    }

    /*
        Debe ajustarse en cada caso
    */
    static function getClient($url = null, $method = null, $exp_time = 21600){
        $cfg  	  = config();

        $exp_time = $cfg['exp_time'] ?? 0;

        $cli   = ApiClient::instance();

        if ($url !== null){
            $cli->setUrl($url);

            if (isset($_GET['clear_cache'])){
                dd("Clearing cache ...");
                $cli->clearCache();   
            }
        }

        if ($method !== null){
            $cli->setMethod($method);
        }

        $cli
        ->withoutStrictSSL()
        ->redirect()
        ->cache($exp_time ?? static::$exp_time);

        ///////////////////////////////////////////

        $cli
        ->setHeaders([
            'User-Agent' => 'PostmanRuntime/7.34.0',
        ]);

        ///////////////////////////////////////////
        
        $cli->setMethod($method);
        $cli->send();
        
        return $cli;        

    }

    static function getHTML($url, $exp_time = null){
        $cli = static::getClient($url, 'GET', $exp_time);        
        $res = $cli->data(); // html

        return $res;
    }

    ///////////////////////////// PRODUCTO ///////////////////////

    private static function getDetailField(string $details, string $fieldName): string
    {
        $pattern = '/' . preg_quote($fieldName, '/') . '\s*([^<]+)/i';
        if (preg_match($pattern, $details, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }

    /*
        Extrae (funcion hecha a la medida) los atributos para productos variables 

        Para Giglio.com debe usarse para obtener los colores posibles dado que
        hay una url distinta por cada color donde es posible seleccionar talla (size)

        --| Selectable attrs (for current variation)
        Array
        (
            [color] => Array
                (
                    [0] => Array
                        (
                            [0] => Grigio
                            [1] => 020
                        )

                    [1] => Array
                        (
                            [0] => Blue
                            [1] => 009
                        )

                    [2] => Array
                        (
                            [0] => Grigio
                            [1] => 020
                        )

                    [3] => Array
                        (
                            [0] => Blue
                            [1] => 009
                        )

                )
            [size] => Array
                (
                    [0] => Array
                        (
                            [0] => 00S
                            [1] => 1054
                        )

                    [1] => Array
                        (
                            [0] => 00M
                            [1] => 1053
                        )

                    [2] => Array
                        (
                            [0] => 00L
                            [1] => 1052
                        )

                )

        )
    */
    static protected function __getVariantAttrs($html, $ucfirst = false)
    {
        $ats = [];

        $dom = new \DOMDocument;
    
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
    
        $xpath = new \DOMXPath($dom);

        $variantDivs = $xpath->query('//div[starts-with(@class, "variant_") and not(contains(@class, "__error"))]');    

        // Iterar sobre los resultados
        foreach ($variantDivs as $div) {
            $inner = $dom->saveHTML($div);
            // ...
            // dd($inner, 'INNER');

            // caso: color
            if ($name = Strings::match($inner, '/class="variant_([a-z]+)__selector/')) {
                // dd($name, 'AT');

                if (!array_key_exists($name, $ats)) {
                    $ats[$name] = [];
                }

                $linkElements = $xpath->query('.//a', $div);

                // Luego, puedes trabajar con $linkElements como lo necesites
                foreach ($linkElements as $link) {
                    $value    = $link->nodeValue;
                    $htmlNode = $dom->saveHTML($link);
                    $dataId   = Strings::matchOrFail($htmlNode, '/cSel=([0-9]{3})/');   
                    
                    if ($ucfirst && !is_numeric($value)){
                        $value = ucfirst(strtolower($value));
                    }

                    if (!in_array($value, array_column($ats[$name], 0))) {
                        $ats[$name][] = [ $value, $dataId ];
                    }
                }
            } else {
                // caso talla:
                $name = Strings::match($inner, '/class="variant_([a-z]+)/');
                // dd($name, 'AT');

                if (!array_key_exists($name, $ats)) {
                    $ats[$name] = [];
                }

                $pattern = '/<option[^>]*\sdata-id="(\d+)"\svalue="([^"]+)"[^>]*>(.*?)<\/option>/';

                preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $dataId = $match[1];
                    $value  = $match[2];

                    if ($ucfirst && !is_numeric($value)){
                        $value = ucfirst(strtolower($value));
                    }

                    if (!in_array($value, array_column($ats[$name], 0))) {
                        $ats[$name][] = [ $value, $dataId ];
                    }
                }
            }           
        }

        // dd($ats); exit;

        return $ats;
    }

    /*
        Devuelve algo como:

        Array
        (
            [current] => 3
            [last] => 16
        )

        En caso de haber pocos productos el paginador podria
        no renderizarse y en cuyo caso se devolvera:

        Array
        (
            [current] => 1
            [last] => 1
        )
    */
    static function getProductPaginator($html)
    {   
        $res = [
            'current' => null,
            'last'    => null
        ];

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Encontrar el contenedor del paginador
        $paginatorContainer = $xpath->query('//div[contains(@class, "products-listing__pagination")]/div[contains(@class, "paginator")]')->item(0);

        if ($paginatorContainer) {
            /** @var \DOMElement $currentUrlNode */
            $currentUrlNode = $xpath->query('.//a[contains(@class, "paginator__now")]', $paginatorContainer)->item(0);

            if ($currentUrlNode) {
                $link = $currentUrlNode->getAttribute('href');

                if (!empty($link)){
                    $res['current'] = Url::getQueryParam($link, 'pag') ?? 1;
                }
            }

            /** @var \DOMElement $lastPageNode */
            $lastPageNode = $xpath->query('.//a[.="»"]', $paginatorContainer)->item(0);

            if ($lastPageNode) {
                $link = $lastPageNode->getAttribute('href');

                if (!empty($link)){
                    $res['last'] = Url::getQueryParam($link, 'pag') ?? 1    ;
                }
            } else {
                $link_count  = $xpath->query('.//a', $paginatorContainer)->count();                
                $res['last'] = !empty($link_count) ? $link_count : 1;
            }
            
        } else {
            $res = [
                'current' => 1,
                'last'    => 1
            ];
        }

        return $res;
    }

     /*
        Devuelve array con todas las URLs de productos de una URL de cateogoria

        Solo para una pagina en particular (la de la URL)
    */
    static /* protected */ function __getProductUrlsByCategory($url)
    {   
        $urls = [];

        $html = static::getHTML($url);

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Utilizar XPath para encontrar la URL del producto
        $productUrls = $xpath->query('//article[contains(@class, "prod-card")]/div/a[contains(@class, "prod-card__url")]/@href');

        // Iterar sobre los resultados e imprimir las URLs
        foreach ($productUrls as $url) {
            $urls[] = static::$urlBase . trim($url->nodeValue);
        }

        return $urls;
    }

    /*
        Devuelve array con todas las URLs de productos de una categoria

        Internamente hace uso del paginador

        Nota:

        Dado que Giglio.com tiene productos variables y cada url de producto
        contiene las variaciones (de talla) por color, 

        ... esta funcion devuelve un Array de esas variaciones 
    
    */
    static function getProductUrlsByCategory($url)
    {
        $html = static::getHTML($url);

        /*
            Ej:

            Array
            (
                [current] => 3
                [last] => 16
            )
        */
        
        $paginator = static::getProductPaginator($html);

        $url_ay = [];
        for ($i=0; $i<$paginator['last']; $i++){
            $page_url = Url::addQueryParam($url, 'pag', $i);
            $url_ay   = array_merge($url_ay, static::__getProductUrlsByCategory($page_url));
        }

        $url_ay = array_unique($url_ay);

        return $url_ay;
    }

    /*
        Extrae todos los colores posibles de un producto variable

        Array
        (
            [0] => Array
                (
                    [0] => grigio
                    [1] => 020
                )

            [1] => Array
                (
                    [0] => blue
                    [1] => 009
                )

            [2] => Array
                (
                    [0] => grigio
                    [1] => 020
                )

            [3] => Array
                (
                    [0] => blue
                    [1] => 009
                )

        )
    */
    static function getColors($html){
        $variant = static::__getVariantAttrs($html, true);
        return $variant['color'] ?? null;
    }

    /*
        Lee un producto (como array) y recupera los atributos de la variacion en particular

        --| Possible variation attr combination (for current color-variant)
        Array
        (
            [mpn] => Array
                (
                    [0] => E71735.020.00S
                    [1] => E71735.020.00M
                    [2] => E71735.020.00L
                )

            [color] => Array
                (
                    [0] => GRIGIO
                )

        )
    */
    static function __extractVariantAttrs(Array $product){
        $variants = $product['variants'] ?? $product['variations'] ?? null;

        if ($variants === null) {
            throw new \Exception('No variants found');
        }

        $arr = [];
        foreach ($variants as $ix => $variant) {
            $attrs = $variant['attributes'] ?? $variant['attrs'] ?? $variant['attr'] ?? [];

            if (empty($attrs)) {
                throw new \Exception("No attributes found. Variant #$ix");
            }
            
            foreach ($attrs as $key => $value) {
                if (!isset($arr[$key])){
                    $arr[$key] = [];
                }

                if (!in_array($value, $arr[$key])) {
                    $arr[$key][] = $value;
                }
            }
        }
        
        return $arr;
    }

    /*
        Atributos "extra" pero "de producto simple" o sea.. no son los que determinan las variantes
    */
    static protected function simpleAttrs($html){
        $dom = new \DOMDocument;
    
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
    
        $xpath = new \DOMXPath($dom);
    
        // Buscar todas las etiquetas <span>
        $spanElements = $xpath->query('//span');
        
        $fields = [];
        // Iterar sobre las etiquetas <span>
        foreach ($spanElements as $spanElement) {
            // Obtener el contenido de la etiqueta <span>
            $htmlNode = $dom->saveHTML($spanElement);

            if (!Strings::contains(':', $htmlNode)) {
                continue;
            }

            $rows = [];

            if (Strings::contains('<br>', $htmlNode)) {
                $rows = explode('<br>', $htmlNode);
            }

            if (Strings::contains('<b>', $htmlNode)) {
                $rows = explode('<b>', $htmlNode);
            }

            foreach ($rows as $ix => $row) {
                $row = trim(strip_tags($row));

                if (empty($row)) {
                    unset($rows[$ix]);
                    continue;
                } 

                $rows[$ix] = $row;
            }

            if (empty($rows)) {
                continue;
            }

            foreach ($rows as $ix => $row) {
                $_f = explode(':', $row);
                $fields[$_f[0]] = trim($_f[1]); 
            }           
        }

    
        return $fields;
    }

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
        $productNameNode = $xpath->query('//h1')->item(0);
        if ($productNameNode) {
            $productDetails['name'] = trim($productNameNode->nodeValue);
        }

        // Extraer descripción del producto
        $descriptionNode = $xpath->query('//section[contains(@class, "product_editorial")]//p')->item(0);
        if ($descriptionNode) {
            $productDetails['desc'] = trim($descriptionNode->nodeValue);

            if (Strings::contains('•', $productDetails['desc'])){
                $desc_items = Strings::lines($productDetails['desc'], true, true, '•');

                $desc_items = array_map(function($item) {
                    return "<li>$item</li>\r\n";
                }, $desc_items);

                $productDetails['desc'] = "<ul>\r\n". implode('', $desc_items) . "</ul>\r\n";
            }
        }

        $productDetails['extra'] = static::simpleAttrs($html);
    
        return $productDetails;
    }

    /*
        Obtiene categoria de producto indidual

        Hay muchas inconcistncias con el slug que a veces se halla dentro de un enlace y otras veces dentro de un span asi que no se considera
    */
    static function getCatego($html){
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

            return $name;
        }
    
        return null;
    }    

    /*
        Obtiene y devuelve las URLs de cada variacion 
        (url parametrizada)
    */
    static function getVariantsUrls($url, $html){
        // lo que deberia hacer es remover el "query para" cSel
        $url       = Strings::beforeIfContains($url,'?');

        $colors    = static::getColors($html);

        /*
            Hay casos donde no hay colores, solo tallas

            Ej:

            https://www.giglio.com/scarpe-uomo_sneakers-salomon-l47376600.html?cSel=002
        */
        if ($colors === null){
            return [ $url ];
        }

        $color_ids = array_column($colors, 1); 

        $urls = [];
        foreach ($color_ids as $id) {
            $urls[] = "$url?cSel=$id";
        }

        return $urls;
    }

    /*
        Devuelve las tallas para cada variacion

        Ej:

        Array
        (
            [0] => M
            [1] => L
            [2] => XL
        )

        Para Giglio.com cuando un producto no tiene tallas, devuelve "talla unica" (U)

        Array
        (
            [0] => U
        )

        Caso:

        https://www.giglio.com/accessori-uomo_cappello-off-white-omlc030f23kni001.html?cSel=009
    */
    static function getVariantSizes($variant_url, $lowercase = false)
    {
        $sizes = [];

        $_p    = GiglioScraper::getProduct($variant_url);
        $attrs = GiglioScraper::__extractVariantAttrs($_p);

        $npms  = $attrs['mpn'];

        foreach ($npms as $mpn) {
            $_mpn = explode('.', $mpn);

            if (count($_mpn) !== 3) {
                throw new \Exception("Unexpected MPN format for '$mpn'");
            }

            $_size = $_mpn[2];

            if ($lowercase) {
                $_size = strtolower($_size);
            }

            if (!in_array($_size, $sizes)) {
                $sizes[] = ltrim($_size, '0');
            }
        }

        return $sizes;
    }

    static function getProduct($url)
    {
        $prod = [];
        
        $html = static::getHTML($url, static::$exp_time);

        if (empty($html)){
            throw new \Exception('Empty HTML');
        }

        if (Strings::contains('<TITLE>Access Denied</TITLE>', $html, false)){
            throw new \Exception('Access Denied');
        }

        if (strlen($html) < 2000){
            throw new \Exception('Access denied or redirection');
        }

        $data = JsonLd::extract($html);

        if (empty($data)){
            if (Strings::contains('<script type="application/ld+json">', $html)){
                throw new \Exception('Error al hacer parsingg del JSON-LD existente');
            }
            
            throw new \Exception('No hay JSON-LD ?'); // mejor seria solo generar un log excepto en modo debug
        }

        // dd($data); exit;

        /*
            Armo la respuesta
        */

        // nombre de producto,..
        $prod = static::getProductBasicAttr($html);

        // cat
        $prod['cat_name'] = static::getCatego($html);

        $prod['extra'] = $prod['attr'] ?? [];
        $prod['extra']['brand']    = $data[0]['brand']['name'] ?? null;  
        $prod['extra']['currency'] = $data[0]['offers']['priceCurrency'] ?? null;  
        $prod['extra']['price_until'] = $data[0]['offers']['priceValidUntil'] ?? null;   
        $prod['extra']['condition'] = str_replace('https://schema.org/', '', $data[0]['offers']['itemCondition']) ?? null;   
        $prod['extra']['seller'] = $data[0]['offers']['seller']['name'] ?? null; 

        // procesamientos adicionales 
        $prod['extra']['condition'] = str_replace('Condition', '', $prod['extra']['condition']);

        /*
            Array
            (
                [color] => Array
                (
                    [0] => Grigio
                )
                [size] => Array
                (
                    [0] => S
                    [1] => M
                    [2] => L
                )
            )
        */
        $prod['attributes'] = [
            'size'  => []  
        ];

        $colors = static::getColors($html);

        // Si hay colores (mas de uno) entonces,... agrego el atributo a la variacion
        if (!empty($colors) && count($colors) > 1){
            $prod['attributes']['color'] = array_column($colors, 0);
        }

        //variations
        $prod['variations'] = [];

        foreach ($data as $ix => $p)
        {
            // variation

            $mpn = $p['mpn'] ?? null;

            if (empty($mpn)){
                throw new \Exception('MPN not found');
            }

            $_f    = explode('.', $mpn);
            $size  = ltrim($_f[2], '0'); 
            $size  = $size == 'U' ? null : $size;

            $color = ucfirst(strtolower($p['color']));

            $stock_status = $p['offers']['availability'] ?? '';
            $stock_status = strtolower(str_replace('https://schema.org/', '', $stock_status));

            if ($stock_status == 'soldout'){
                $stock_status = 'outofstock';
            }

            $prod['variations'][] = [                
                'name'           => $p['name'] ?? null,
                'sku'            => $p['sku'] ?? null,
                'description'    => $p['description'] ?? null,
                'images'         => $p['image'] ?? null,
                'featured_image' => $p['image'][0] ?? null,
                'price'          => $p['offers']['price'] ?? null,
                'stock_status'   => $stock_status,  

                'attributes' => [
                    'size'  => $size,
                    'color' => $color,
                ]
            ];

            if ($size != null && !in_array($size, $prod['attributes']['size'])){
                $prod['attributes']['size'][] = $size;
            }
        }
        
        if (empty($prod['attributes']['size'])){
            unset($prod['attributes']['size']);

            foreach ($prod['variations'] as $k => $v){
                unset($prod['variations'][$k]['attributes']['size']);
            }

        }

        // exit;
        //dd( $prod['attributes']); exit;
        // dd($prod); exit;

        return $prod;                 
    }

    ////////////////////////////////////////////////////////////
    //////////////////////              ////////////////////////
    //////////////////////  CATEGORIES  ////////////////////////
    //////////////////////              ////////////////////////
    ////////////////////////////////////////////////////////////

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

    static function getCategosList($html = null) 
    {
        $cached_list = get_transient('catego_list');

        if ($cached_list){
            dd("Using cache for category list");
            return $cached_list;
        }

        if (empty($html)){
            $html = static::getHomePage();
        }

        $main_categos = static::getFirstLevelCategos($html);

        $main_categos_ay = [];
        foreach ($main_categos as $slug) {
            $main_categos_ay[] = [
                'slug' => $slug,
                'name' => ucfirst(str_replace('/', '', $slug)),
            ];
        }

        $arr = $main_categos_ay;
        foreach ($main_categos as $main_cat) {
            $url  = static::$urlBase . $main_cat;

            // obtengo el html de las categorias principales
            $html = static::getHTML($url, static::$exp_time);

            $arr  = array_merge($arr, static::getCategos($html));
        }

        foreach ($arr as $ix => $cat){
            if (!isset($cat['slug']) || empty($cat['slug'])){
                continue;
            }

            $arr[$ix]['link'] = static::$urlBase . $cat['slug'];
        }

        
        set_transient('catego_list', $arr, config()['categor_exp_time']);

        return $arr;
    }

    /*
        Similar en formato a getCategosList() pero no deberian insertarse 
        sino usarse de forma temporal para recuperar mas productos

        Usar getCategosList() primero y luego getBrandList()

        A los productos no se les debe cambiar el titulo ni categoria ni descripcion
        una vez asignados
    */
    static function getBrandList($html = null) 
    {
        $main_categos = static::getFirstLevelCategos($html);

        $arr = [];
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


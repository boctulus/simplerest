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
    ✅ Pagina de producto (faltan precios variaciones)  getProduct

    getCategoList -> getPaginator -> getCategoryPageURL -> getProductLink -> getProduct
*/
class StratoScraper extends ProductScraper
{
    protected static $objectID;
    protected static $baseUrl = 'https://www.azulejosmadridonline.es/epages/63993920.sf/es_ES/';


    public static function getBaseURL(){
        return static::$baseUrl;
    }

    /*
        Obtiene paginador de pagina de productos (u otras)

        Ej:

        Array
        (
            [totalResults] => 127
            [totalPages] => 3
            [currentPage] => 1
        )

        TO-DO

        - Generar salida basada en Interfaz y validaciones (opcional)
    */
    public static function getPaginator($html){
        $crawler = new DomCrawler($html);
    
        // Encontrar el elemento que contiene la información del paginador
        $paginatorElement = $crawler->get('.PagedProduct .PagerTable');
    
        // Obtener el texto que contiene la información de resultados
        $paginatorInfoText = $paginatorElement->getText('.PagerInfoLeft');

        if ($paginatorInfoText === null){
            return [
                'totalResults' => false, // indeterminado
                'totalPages'   => 1,
                'currentPage'  => 1
            ];
        }
    
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
        $currentPageElement = $paginatorElement->get('.PagerSizeContainer li.Current');
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

        return static::$baseUrl . "?ViewAction=View&ObjectID=$object_id&PageSize=$page_size&Page=$page";
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
        A diferencia de getCategoList(), esta lista contiene solo URLs
        y no esta "estructurada"

        Puede ser muchas veces una lista mas completa.
    */
    public static function getCategoURLs(string $html) {        
        $crawler = new DomCrawler($html);
        
        // Encuentra todos los enlaces <a>
        $links = $crawler->get('a')->each(function (DomCrawler $node) {
            return $node->attr('href');
        });
        
        // Filtra los enlaces que comienzan con "?ObjectPath=/Shops/63993920/Categories/"
        $filteredLinks = array_filter($links, function ($url) {
            return preg_match('/^\?ObjectPath=\/Shops\/63993920\/Categories\//', $url);
        });
        
        return array_unique($filteredLinks);
    }

    public static function getCatego(string $html)
    {
        $crawler = new DomCrawler($html);
        
        // Encuentra el elemento que contiene la información de la categoría
        $categoryElement = $crawler->get('.CategoryList');

        // Extrae el nombre de la categoría
        $categoryName = $categoryElement->getText('.CategoryText i b');
        
        // Extrae la descripción de la categoría
        $categoryDescription = $categoryElement->getHtml('.CategoryText');

        // Encuentra todas las imágenes de la categoría
        $imageElements = $categoryElement->get('.CategoryImage img');
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

    static function getCategoURLs__Filtered(){
        return require /* Constants:: */ ETC_PATH . '/filtered_catego_list.php';
    }

    /*
        Notas:

        - Hago un urldecode para que el slug sea comprensible
        
        - Omito por brevedad la primera parte de '?ObjectPath=/Shops/63993920/Categories/'
    */
    static function getParentCategory(string $category_url) {
        $slugs = explode('/', Strings::afterOrFail($category_url, '/Categories/'));

        if (count($slugs) == 1){
            return null; // ROOT
        };

        // '?ObjectPath=/Shops/63993920/Categories/' .
        return urldecode($slugs[count($slugs)-2]);
    }

    /*  ...,
        (
            [name] => Ferretería
            [slug] => ferreteria-materiales-de-construccion
            [parent_slug] => 
            [url] => ?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22
        ),
        ....
    */
    public static function getCategoList(?string $html = null){
        $urls = static::getCategoURLs__Filtered();

        $arr = [];
        foreach ($urls as $url){
            $slugs = explode('/', Strings::afterOrFail($url, '/Categories/'));
            $slug  = $slugs[count($slugs)-1]; 
            $name  = urldecode($slug);

            $slug_parent = null;

            if (count($slugs) > 1){
                $slug_parent = $slugs[count($slugs)-2]; 
            };

            $name = Strings::accents2Ascii($name);
            $name = str_replace('"', '', $name);
            $name = str_replace('_', ' ', $name);
            $name = str_replace('-', ' ', $name);
            $name = Strings::removeMultipleSpaces($name);
            $name = ucfirst(strtolower($name));

            $arr[] = [
                'name' => $name,
                'slug' => $slug,
                'parent_slug' => $slug_parent,
                'url' => $url
            ];
        }

        return $arr;
    }
    

    /*
        ?ObjectPath=/Shops/63993920/Products/arsa32x89
        ?ObjectPath=/Shops/63993920/Products/itq75x22
        ?ObjectPath=/Shops/63993920/Products/meba75x22
        ...
    */
    static function getProductLinks(string $html)
    {
        $_urls = (new DomCrawler($html))->getLinks();

        $urls  = [];
        foreach ($_urls as $url){
            if (Strings::startsWith('?ObjectPath=/Shops/63993920/Products/', $url)){
                $urls[] = $url;
            } 
        }

        return array_unique($urls);
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
        $container = $crawler->get('table.VariationsTable');
    
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
    /**
     * Extrae los slugs de productos relacionados.
     *
     * @param string $html
     * @return array
     */
    public static function extractRelatedProducts(string $html): array
    {
        $arr = [];

        // Crear un objeto Crawler para analizar el HTML
        $crawler = new DomCrawler($html);

        // Obtener todos los enlaces dentro de los contenedores div.CrossellingImageArea
        $links = $crawler->getAttributes('div.CrossellingImageArea a', 'href');

        foreach ($links as $link) {
            $arr[] = Strings::match($link, '|Shops/[0-9]+/Products/([a-z0-9%]+)|i');
        }

        return $arr;
    }

    static function __getVariantAttrs($html, $ucfirst = false)
    {
        /*
            Devuelve atributos de un producto variable

            Es compatible con la interfaz de Giglio

            [attributes] => Array
            (
                [Medida] => Array
                (
                    [125988553] => 60x45x60
                    [125988558] => 80x45x60
                    [125988563] => 100x45x60
                    [125988568] => 120x45x60 Dos muebles.
                )
            )
        */

        $variations = [];
    
        // Crear un objeto Crawler para analizar el HTML
        $crawler = new DomCrawler($html);
    
        // Seleccionar todas las tablas con la clase "SelectVariation"
        $crawler->get('table.SelectVariation')->each(function ($table) use (&$variations, &$ucfirst) {
            // Recuperar todos los <th> con sus correspondientes 'for' y textos
            $headers = [];
            $table->get('th')->each(function ($th) use (&$headers) {
                $label = $th->get('label');
                if ($label->count() > 0) {
                    $for = $label->attr('for');
                    $text = trim($label->text());
                    $headers[$for] = $text;
                }
            });
    
            // Recorrer todos los <td> y verificar los <select>
            $table->get('td')->each(function ($td) use (&$headers, &$variations, &$ucfirst) {
                $select = $td->get('select');
                if ($select->count() > 0) {
                    $id = $select->attr('id');
                    if (isset($headers[$id])) {
                        $variationName = $headers[$id];
                        $options = [];
                        $select->get('option')->each(function ($option) use (&$options, &$ucfirst) {
                            $value = $option->attr('value');
                            $text = trim($option->text());

                            if ($ucfirst && !is_numeric($value)){
                                $value = ucfirst(strtolower($value));
                            }

                            if (!empty($value)){
                                $options[$value] = $text;
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
    public static function getProduct(string $url){
        if (Strings::startsWith('/Products/', $url)){
            $html = static::getHTMLfromSlug($url);  
        } else {
            $html = static::getHTML($url);   
        }

        preg_match_all('/data-src-l="([^"]+)"/', $html, $matches);
        $image_urls = $matches[1];

        foreach ($image_urls as $ix => $im_url){
            $image_urls[$ix] = 'https://www.azulejosmadridonline.es' . $im_url; 
        }

        $crawler = new DomCrawler($html);       

        // Extraer información del producto
        $productName = $crawler->getText('h1[itemprop="name"]');
        $itemCondition = $crawler->getAttr('meta[itemprop="itemCondition"]','content');
        $sku = $crawler->getAttr('meta[itemprop="sku"]','content');
        $manufacturer = $crawler->getAttr('meta[itemprop="manufacturer"]','content');
        $brand = $crawler->getAttr('meta[itemprop="brand"]','content');
        $category = $crawler->getAttr('meta[itemprop="category"]','content');
        $description = $crawler->getHTML('.description[itemprop="description"]');

        # Unit. Puede ser "m²" entre otras opciones
        $dataJs = $crawler->getAttr('input[name="Quantity"]', 'data-js');
        $unit   = empty($dataJs) ? null : Strings::match($dataJs, '/unit:\'([^\']+)\'/'); 

        $shippingMethodsBlockText = $crawler->getText('span.ProductShippingMethods');
    
        // Obtener el contenedor de precios
        $priceContainer = $crawler->get('.PriceContainer');

        // Obtener el precio regular (regular price)
        $regularPrice = $priceContainer->text('.AlignLeft');
        if (!empty($regularPrice)){
            $regularPrice = preg_replace('/¡OFERTA!, antes: | €/', '', $regularPrice);
            $regularPrice = trim($regularPrice);
            $regularPrice = Strings::before($regularPrice, ' ');
            $regularPrice = str_replace('.AlignLeft', '', $regularPrice);
            $regularPrice = str_replace(',', '.', $regularPrice);
        }        

        // Obtener el precio de oferta (sale price)
        $salePrice = $priceContainer->text('.Price .price-value');

        if (!empty($salePrice)){
            $salePrice = preg_replace('/¡OFERTA!, antes: | €/', '', $salePrice);
            $salePrice = trim($salePrice);
            $salePrice = Strings::before($salePrice, ' ');
            $salePrice = str_replace('.Price', '', $salePrice);
            $salePrice = str_replace(',', '.', $salePrice);
        }

        $regularPrice  = Strings::parseNumeric($regularPrice);
        $salePrice     = Strings::parseNumeric($salePrice);

        if ($salePrice == $regularPrice){
            $salePrice = '';
        } else if ($salePrice > $regularPrice){
            throw new \Exception("salePrice > regularPrice with `$salePrice` vs. `$regularPrice` euros");
        }

        if (empty($regularPrice)){
            $regularPrice = $crawler->getAttr('span.price-value span[itemprop="price"]', 'content');
        }

        $stock_status   = null;
        $available_in_t = null;

        if (Strings::contains('NO DISPONIBLE EN STOCK', $html)){
            $stock_status = 'outofstock';
        } else {
            if (Strings::contains("En existencias", $html)){
                $stock_status = 'instock';
            } else {
                $stock_status = $crawler->exists('button[name="AddToBasket"]') ? 'instock' : 'outofstock';
            }
        }

        $available_in_t = Strings::match($html, "/se puede enviar en ([0-9]{1,2}-[0-9]{1,2})/");

        /*
            Post-procesamientos
        */

        $itemCondition = static::extractCondition($itemCondition);
        $itemCondition = str_replace('Condition', '', $itemCondition);

    
        $extra = [
            'brand'         => $brand,
            'manufacturer'  => $manufacturer,
            'currency'      => 'EUR',
            'price_until'   => null,
            'condition'     => $itemCondition,
            'unit'          => $unit,
            'in_t_days'     => $available_in_t,               
            'shipping_text' => $shippingMethodsBlockText,
        ];

        // Imprimir la información del producto
        $p = [
            'name'          => $productName,
            'sku'           => $sku,            
            'category'      => $category,
            'description'   => $description,
            'images'        => $image_urls,
            'regular_price' => $regularPrice,
            'sale_price'    => $salePrice,
            'accesories'    => static::extractAccessories($html),     //
            'related'       => static::extractRelatedProducts($html), //
            'stock_status'  => $stock_status,             
            'attributes'    => static::__getVariantAttrs($html),
            'extra'         => $extra
        ];

        $p['featured_image'] = $p['images'][0] ?? null;

        $p['type'] = empty($p['attributes']) ? 'simple' : 'variable';

        return $p;
    }


    ///////////////////////// AUXILIARES //////////////////////////////

    /*
        CREATE TABLE `woo8`.`wp_paginated_catego_urls` (
            `id` INT NOT NULL AUTO_INCREMENT , 
            `category` VARCHAR(240) NOT NULL , 
            `paginated_category` VARCHAR(240) NOT NULL , 
            PRIMARY KEY (`id`)
        ) ENGINE = MyISAM;

        ALTER TABLE `wp_paginated_catego_urls` ADD UNIQUE(`paginated_category`);
    */
    static function savePaginatedCategoURLs()
    {
        StratoScraper::setup();

        $base_url    = 'https://www.azulejosmadridonline.es/epages/63993920.sf/es_ES/';

        $catego_urls = StratoScraper::getCategoURLs__Filtered();

        foreach ($catego_urls as $cat_url){
            $cat_url   = "{$base_url}{$cat_url}";

            $html      = StratoScraper::getHTML($cat_url);
            $paginator = StratoScraper::getPaginator($html);

            $tot_pages = $paginator['totalPages'];

            if ($tot_pages == 0){
                dd("Skiping category '$cat_url' because has not even one page");
            }

            foreach (range(1, $tot_pages) as $page){
                $paginated_cat_url = $base_url . StratoScraper::getCategoryPageURL($cat_url, $page);
                
                $row = [
                    'category'           => $cat_url,
                    'paginated_category' => $paginated_cat_url
                ];

                $id  = table('paginated_catego_urls')
                ->insert($row);

                dd($row, "ID=$id");
            }	
        }
    }

    /*
        CREATE TABLE `woo8`.`wp_product_urls` (
            `id` INT NOT NULL AUTO_INCREMENT , 
            `product_url` VARCHAR(240) NOT NULL , 
			`cat_id` INT(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE = MyISAM;

        ALTER TABLE `wp_product_urls` ADD UNIQUE(`product_url`);
        ALTER TABLE `wp_product_urls` ADD INDEX(`cat_id`);
    */
    static function saveProductURLs()
    {
        $paginated_cats = table('paginated_catego_urls')->get();

        foreach ($paginated_cats as $cat)
        {
            $cat_id  = $cat['id'];
            $cat_url = $cat['paginated_category'];

            $html   = StratoScraper::getHTML($cat_url);
            $links  = StratoScraper::getProductLinks($html);
        
            foreach ($links as $link){
                $row = [
                    'product_url' => Strings::beforeIfContains($link, '&'),
                    'cat_id' => $cat_id
                ];

                $id  = table('product_urls')
                ->insert($row);

                dd($row, "ID=$id");
            }
            
        }    
    }

    static function getCategoURLs__static()
    {
    return [
            0 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22',
            1 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Azulejos',
            2 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Azulejos/Blancos',
            3 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Azulejos/Azulejos_rusticos',
            4 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Azulejos/Azulejos_rectificados',
            5 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico',
            6 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_Hidraulicos',
            7 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Pagina__Categoria',
            8 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_hexagonales',
            9 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_marmoleados',
            10 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_gran_formato',
            11 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_antideslizantes',
            12 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_20x20',
            13 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_32x62',
            14 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanico_efecto_metal',
            15 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_60x60_y_66x66',
            16 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_tipo_piedra__ladrillo',
            17 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_escama_de_pez',
            18 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Material_Porcelanico/Porcelanicos_tipo_terrazo',
            19 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Gres_de_Aragon',
            20 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Ofertas_en_azulejos_y_porcelanicos',
            21 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Piscinas',
            22 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Piscinas/Vasos_piscina',
            23 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante',
            24 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/MODELOS_EN_STOCK',
            25 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/APAVISA_APARICI',
            26 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/CERAMICA_RIBESALBES',
            27 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/CERLAT',
            28 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/Codicer',
            29 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/GAYAFORES',
            30 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/HALCON_CERAMICA',
            31 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/HDC_porcelanico',
            32 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/MAYOLICA_AZULEJOS',
            33 => '?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Productos_por_fabricante/Realonda_Ceramica',
            34 => '?ObjectPath=/Shops/63993920/Categories/Productos_enmallados',
            35 => '?ObjectPath=/Shops/63993920/Categories/Productos_enmallados/Mallas_de_cristal',
            36 => '?ObjectPath=/Shops/63993920/Categories/Productos_enmallados/Mallas_para_platos_de_ducha',
            37 => '?ObjectPath=/Shops/63993920/Categories/Productos_enmallados/Gresite__Mosaico_vitreo',
            38 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1',
            39 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Cenefas_para_pavimentos',
            40 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Cenefas_para_pavimentos/Cenefas_porcelanicas_15x33',
            41 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Cenefas_para_pavimentos/Cenefas_pavimentos_333cms',
            42 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Cenefas_ceramicas_65x25',
            43 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Cenefas_hidraulicas',
            44 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Cenefas_sin_fin_5x20',
            45 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Decorados',
            46 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Decorados/Decorados_Tecniker_30x30',
            47 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Decorados/Decorado_Cubik_20x20',
            48 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Decorados/Decorado_Hidraulico',
            49 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Decorados/Decorados_Digitalker_20x60_y_30x60',
            50 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Decorados/Composiciones_decorados',
            51 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Azulejos_Decorados',
            52 => '?ObjectPath=/Shops/63993920/Categories/Cenefas_de_ceramica1/Torelos__Piezas_especiales',
            53 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22',
            54 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Platos_de_ducha_carga_mineral',
            55 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Platos_de_ducha_carga_mineral/Platos_de_ducha_carga_mineral_STONE_PLUS',
            56 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Platos_de_ducha_carga_mineral/Platos_de_ducha_carga_mineral_STONE_MIO',
            57 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Platos_de_ducha_carga_mineral/Platos_ducha_carga_mineral_STONE_SIDE',
            58 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Platos_de_ducha_carga_mineral/Plato_ducha_carga_mineral_STONE_SMART',
            59 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Platos_de_ducha_carga_mineral/Plato_ducha_carga_mineral_Stone_CACH_CA',
            60 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Platos_de_ducha_carga_mineral/Platos_ducha_carga_mineral_STONE_SORTY',
            61 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Platos_de_ducha_carga_mineral/Platos_ducha_carga_mineral_STONE_3D',
            62 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/MUEBLES_DE_BANO',
            63 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/MUEBLES_DE_BANO/%22Mueble%20de%20ba%C3%B1o%20ETNA%22',
            64 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/MUEBLES_DE_BANO/Mueble_de_bano_serie_KIOTO',
            65 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/MUEBLES_DE_BANO/Mueble_de_bano_serie_ZAO',
            66 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/MUEBLES_DE_BANO/Mueble_de_bano_serie_TEIDE',
            67 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/MUEBLES_DE_BANO/Mueble_de_bano_serie_NASU',
            68 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Griferia',
            69 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Griferia/Griferia_lavabos',
            70 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/Griferia/Duchas',
            71 => '?ObjectPath=/Shops/63993920/Categories/%22BA%C3%91OS%22/MAMPARAS',
            72 => '?ObjectPath=/Shops/63993920/Categories/Griferia',
            73 => '?ObjectPath=/Shops/63993920/Categories/Griferia/Grifos_de_cocina',
            74 => '?ObjectPath=/Shops/63993920/Categories/Griferia/Grifos_de_bano',
            75 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22',
            76 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Soluciones_para_peldanos',
            77 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Soluciones_para_esquinas',
            78 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Proteccion_y_seguridad',
            79 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Accesorios_proteccion_de_obra',
            80 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Impermeabilizaci%C3%B3n',
            81 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Sistema_nivelacion_pavimentos',
            82 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/%22Corte%2C%20taladro%20para%20materiales%20de%20construcci%C3%B3n%22',
            83 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Fijacion__Tornilleria__Anclaje',
            84 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Herramientas_manuales',
            85 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Platos_de_ducha_de_obra',
            86 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Adhesivos__Siliconas__Qu%C3%ADmicos',
            87 => '?ObjectPath=/Shops/63993920/Categories/%22Ferreter%C3%ADa%2C%20materiales%20de%20construcci%C3%B3n%22/Productos_limpieza_de_obra',
            88 => '?ObjectPath=/Shops/63993920/Categories/%22Ofertas%20-%20Outlet%22',
        ];
    }


}


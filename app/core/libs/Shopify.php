<?php

namespace simplerest\core\libs;

use simplerest\core\libs\DB;

/*
    Hacer uso de un controlador o de rutas para procesar Webhooks

    Ej:

        ShopifyController

    Notas:

    When Shopify sends request to your webhook url. it will also send some extra details in HTTP header.

    You can find X-Shopify-Shop-Domain header which will contain domain name of store. From domain name you can extract name of store.

    it also contains X-Shopify-Hmac-SHA256 to verify aunthenticity of request.

    https://stackoverflow.com/questions/29567824/how-to-get-shopify-shop-id-inside-of-product-update-webhook

    Tanto la libreria como el Controlador se han generalizado por que podrian empaquetarse en un package !!!!
*/
class Shopify
{
    protected $api_key;
    protected $api_secret;
    protected $api_ver;
    protected $shop;

    protected $shopify_to_woocommerce_eq = [
		'Title'                     => 'name',
		'Body (HTML)'               => 'description',
		'Quantity'                  => 'stock_quantity',
		'Variant Inventory Qty'     => 'stock_quantity',
		'Image Src'                 => 'images',
		'Variant Image'             => 'images',
		'Variant SKU'               => 'sku',
		'Variant Price'             => 'sale_price',
		'Variant Compare At Price'  => 'regular_price',
		'Type'                      => 'category_ids',
		'Tags'                      => 'tag_ids_spaces',
		'Variant Grams'             => 'weight',
		'Variant Requires Shipping' => 'meta:shopify_requires_shipping',
		'Variant Taxable'           => 'tax_status',
    ];

    function __construct($shop = null, $api_token = null, $api_key = null, $api_secret = null, $api_ver = null)
    {
        $this->shop       = $shop;                
        $this->api_key    = $api_key;
        $this->api_secret = $api_secret;
        $this->api_ver    = $api_ver;

        /*
            Array utilizado por ShopifyController::shopify_create_webooks()
        */
    
        if (!empty($shop) && !empty($api_token) && !empty($api_key) && !empty($api_secret) && !empty($api_ver)) {
            Config::set('shopify', [
                'api' => [
                    $shop => [
                        'api_token'  => $api_token,
                        'api_key'    => $api_key,
                        'api_secret' => $api_secret,
                        'api_ver'    => $api_ver
                    ]
                ]
            ]);
        }
    }

    function getProducts($max_rows = null){
        set_time_limit(0);
        
        $api_key     = $this->api_key;
        $api_secret  = $this->api_secret;
        $api_ver     = $this->api_ver;
        $shop        = $this->shop;

        // usar since_id para "paginar"
        $endpoint = "https://$api_key:$api_secret@$shop.myshopify.com/admin/api/$api_ver/products.json";

        $limit    = 100;  // 100
        $last_id  = 0;
        $query_fn = function($limit, $last_id){ return "limit=$limit&since_id=$last_id"; };

        // número de páginas
        $count   = 0; 
        while(true){
            if (isset($max_rows) && !empty($max_rows) && ($count >= $max_rows/$limit)){
                break;
            }
    
            //dd("Q=" . $query_fn($limit, $last_id));

            $res = consume_api("$endpoint?".$query_fn($limit, $last_id), 'GET');
    
            if ($res['http_code'] != 200){
                //dd($res['error'], 'ERROR', function(){ die; });
                return [
                    'error' => $res['error']
                ];
            }
    
            $data       = json_decode($res['data'], true); 

            $products[] = $data["products"];       
            
            $last_id    = max(array_column($products, 'id'));

            if (count($products) != $limit){
                break;
            }

            $count++;            
        }    

        return $products;
    }

    function getCollectionsByProductId($shop, $product_id, $api_key, $api_secret, $api_ver)
    {
        $endpoint = "https://$api_key:$api_secret@$shop.myshopify.com/admin/api/$api_ver/collects.json?product_id=$product_id";

        $res = consume_api($endpoint, 'GET');

        if (!isset($res['data']["collects"])){
            return;
        }
        
        $col_ids = array_column($res['data']["collects"], "collection_id");

        $coll_names = [];
        foreach ($col_ids as $col_id){
            $endpoint = "https://$api_key:$api_secret@$shop.myshopify.com/admin/api/$api_ver/custom_collections/$col_id.json";

            $res = consume_api($endpoint, 'GET');
            $obj = $res['data']["custom_collection"];

            if ($obj['handle'] != "frontpage"){
                $coll_names[] = $obj['title'];
            }
        }

        return $coll_names;
    }
    
    function getWebHook($shop, $entity, $operation){
        static $webhooks = [];

        $api_key     = $this->api_key;
        $api_secret  = $this->api_secret;
        $api_ver     = $this->api_ver;
        $shop        = $this->shop;

        if (empty($webhooks)){
            $endpoint = "https://$api_key:$api_secret@$shop.myshopify.com/admin/api/$api_ver/webhooks.json";
            
            $res = consume_api($endpoint, 'GET');

            if (empty($res)){
                return;
            }

            if (!isset($res["data"]["webhooks"])){
                return;
            }

            $webhooks = $res["data"]["webhooks"];
        }

        $topic    = "$entity/$operation";

        foreach ($webhooks as $wh){
            if ($wh["topic"] == $topic){
                return $wh;
            }
        }

        return false;
    }

    function webHookExists($shop, $entity, $operation){
        $wh = $this->getWebHook($shop, $entity, $operation);
        return !empty($wh);
    }

    function createWebhook($shop, $entity, $operation, $check_before = true){
        $base_url = Url::getBaseUrl();

        if (!Strings::startsWith('https://', $base_url)) {
            dd("La url callback para los webhooks debe ser https y es ". $base_url);
        }

        $exists = $this->webHookExists($shop, $entity, $operation);

        if ($check_before && $exists){
            return;
        }

        $api_key     = $this->api_key;
        $api_secret  = $this->api_secret;
        $api_ver     = $this->api_ver;
        $shop        = $this->shop;

        $topic    = "$entity/$operation";
        $endpoint = "https://$api_key:$api_secret@$shop.myshopify.com/admin/api/$api_ver/webhooks.json";

        $body = [
                "webhook" => [
                "topic"   => $topic,  
                "address" => $base_url . "/index.php/wp-json/connector/v1/shopify/webhooks/{$entity}_{$operation}",
                "format"  => "json"
                ]
        ];

        //dd($endpoint, 'ENDPOINT');
        //dd($body, 'BODY');

        $res = consume_api($endpoint, 'POST', $body, [
            $api_key => $api_secret
        ]);

        if (empty($res)){		
            return [
                'error' => "Error al crear WebHook para $topic"
            ];
        }

        if (!isset($res['data']['webhook']) /*|| isset($data['id']) */){
            return [
                'error' => "Error en la respuesta al crear WebHook para $topic"
            ];
        }

        return true;
    }

    function deleteAllWebhooks()
    {    
        $api_key     = $this->api_key;
        $api_secret  = $this->api_secret;
        $api_ver     = $this->api_ver;
        $shop        = $this->shop;
    
        $operations = ['create', 'update', 'delete'];

        foreach ($operations as $operation){
            $wh = $this->getWebHook($shop, 'products', $operation);

            if ($wh != null && isset($wh['id'])){
                // DELETE /admin/api/2021-07/webhooks/1056452214977.json

                $id = $wh['id'];
                $endpoint = "https://$api_key:$api_secret@$shop.myshopify.com/admin/api/$api_ver/webhooks/{$id}.json";

                $res = consume_api($endpoint, 'DELETE');
                dd($res, $shop);
            }
        }
    }

    /*
        Extras
    */

    /*
        Convierte la estructura de productos de Shopify a la de WooCommerce

        La estructura puede haber cambiado revisa el array que proviene del source code de WooCommerce:

        $this->shopify_to_woocommerce_eq
    */
    function adaptToWooCommerce(Array $a, $shop, $api_key, $api_secret, $api_ver){

        $pid = $a['id'];

        $a['name'] = $a['title'];
        $a['description'] = $a['body_html'];

        // Visibility ('hidden', 'visible', 'search' or 'catalog')
        if ($a['published_scope'] == 'web'){
            $a['visibility'] = 'visible';
        } else {
            $a['visibility'] = 'hidden';
        }	

        /*
            status

            En WooCommerce puede ser publish, draft, pending
            En Shopify serían active, draft, archived
        */

        $a['status'] = $this->convertStatusFromShopifyToWooCommerce($a['status']);
        
        /*
            tags

            En WooCommerce es un array
            En Shopify están separados por ", "
        */

        $tags = explode(',', $a['tags']);
        
        foreach ($tags as $k => $tag){
            $tags[$k] = trim($tag);
        }

        $a['tags'] = $tags;


        // Categories

        $a['categories'] = [];

        if (isset($a["product_type"]) && !empty($a["product_type"])){
            $a['categories'][] = 	$a["product_type"];
        }

        $a['categories'] = array_merge($a['categories'], $this->getCollectionsByProductId($shop, $pid, $api_key, $api_secret, $api_ver) ?? []);

        /*
            Variations as simple products
        */

        $vars = [];
        foreach($a['variants'] as $k => $v){

            // atributos

            /*
                Deben seguir la estructura de los atributos para productos simples de WooCommerce:

                'attributes' => 
                    array (
                        'pa_talla' => 
                        array (
                        ),
                        'pa_color' => 
                        array (
                        ),
                    )
            */		
            $attributes = [];

            foreach ($a['options'] as $i => $op){
                $name  = $op['name'];
                $value = $v['option' . ($i +1)];
                $term  = strtolower($name);

                if ($term == 'size'){
                    $term = 'talla';
                } elseif ($term == 'style'){
                    $term = 'estilo';
                } elseif ($term == 'title'){
                    continue;
                }

                $term = 'pa_' . $term;

                $attributes[ $term ] = [ $value ];
            }

            $name = $a['name'];

            if ($v['title'] != 'Default Title'){
                $name = $name . ' - ' . $v['title'];
            }

            $vars[$k] = [
                'type'				=> 'simple',
                'name'       		=> $name,
                'description' 		=> $a['description'],
                'visibility'  		=> $a['visibility'],
                'status'      		=> $a['status'],
                'tags'        		=> $a['tags'],
                'categories'  		=> $a['categories'],
                'regular_price'		=> $v['price'],
                'sale_price'  		=> $v['compare_at_price'],
                'sku'         		=> $v['sku'],
                'weight'	  		=> $v['weight'],
                'stock_quantity' 	=> $v['inventory_quantity'],
                'manage_stock'      => $v['inventory_management'] !== NULL,
                //'tax_status'		=> $v['taxable'] ? 'taxable' : 'none',
                'attributes' 		=> $attributes
            ];
        

            foreach ($a['images'] as $img){
                foreach ($img['variant_ids'] as $vid){
                    if ($vid == $v['id']){
                        $vars[$k]['image'][0] = $img['src'];
                        $vars[$k]['image'][1] = $img['width'];
                        $vars[$k]['image'][2] = $img['height'];
                        break 2;
                    }
                }
            }

        }

        // si es un producto "simple"
        if (isset($a['image']) && $a['variants'][0]['title'] == 'Default Title'){            
            $vars[0]['image'] = [
                $img['src'],
                $img['width'],
                $img['height']
            ];
        }
        
        return $vars;
    }

    /*
		Status

		En WooCommerce puede ser publish, draft, pending
		En Shopify serían active, draft, archived
	*/
    function convertStatusFromShopifyToWooCommerce(string $status, bool $strict = false){
        $arr = [
            'active'   => 'publish',
            'archived' => 'draft',
            'draft'    => 'draft' 
        ];

        if (in_array($status, $arr)){
            return $arr[$status];
        }

        if ($strict){
            throw new \InvalidArgumentException("Status $status no válido para Shopify");
        }

        return $status;
    }

    function convertStatusFromWooCommerceToShopify(string $status, bool $strict = false) {
        $arr = [
            'publish' => 'active',
            'draft'   => 'draft', 
            'pending' => 'draft'
        ];

        if (in_array($status, $arr)){
            return $arr[$status];
        }

        if ($strict){
            throw new \InvalidArgumentException("Status $status no válido para Shopify");
        }

        return $status;
    }
}


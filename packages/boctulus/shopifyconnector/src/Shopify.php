<?php

namespace Boctulus\ShopifyConnector;

use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\libs\Strings;

// use Boctulus\ShopifyConnector\Traits\ShopifyWooCommerceAdaptorTrait;

/*
    Hacer uso de un controlador o de rutas para procesar Webhooks

    Ej:

        ShopifyController

    Notas:

    When Shopify sends request to your webhook url. it will also send some extra details in HTTP header.

    You can find X-Shopify-Shop-Domain header which will contain domain name of store. From domain name you can extract name of store.

    it also contains X-Shopify-Hmac-SHA256 to verify aunthenticity of request.

    https://stackoverflow.com/questions/29567824/how-to-get-shopify-shop-id-inside-of-product-update-webhook!
*/
class Shopify
{
    // use ShopifyWooCommerceAdaptorTrait;
    
    protected $api_key;
    protected $api_secret;
    protected $api_ver;
    protected $shop;


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

    function getProducts(int $max_rows = null): array
    {
        set_time_limit(0);

        $endpoint = "https://{$this->shop}.myshopify.com/admin/api/{$this->api_ver}/products.json";
        $limit = 100;
        $products = [];
        $page_info = null;

        do {
            if (isset($max_rows) && count($products) >= $max_rows) {
                break;
            }

            $query = http_build_query(['limit' => $limit, 'page_info' => $page_info]);
            $response = consume_api("$endpoint?$query", 'GET', [], [
                'X-Shopify-Access-Token' => $this->api_key
            ]);

            if ($response['http_code'] !== 200) {
                return [
                    'error' => $response['error'] ?? 'Unknown error'
                ];
            }

            $data = json_decode($response['data'], true);
            $products = array_merge($products, $data['products']);
            $page_info = $data['page_info'] ?? null;

        } while ($page_info);

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
                // dd($res, $shop);
            }
        }
    }
    
}


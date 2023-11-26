<?php

namespace simplerest\controllers\honeys;

use simplerest\core\libs\Logger;
use simplerest\core\libs\Shopify;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyController;

/*
    Para hacer pruebas con WebHooks es posible usar NgrOk
    que proveera de una url como https://f920c96f987d.ngrok.io

    De pagar suscripcion, la url no es temporal

    Cada webhook comienza con "shopify_webhook_"
*/
class ShopifyController extends MyController
{
    protected $data;

    function __construct()
    {
        $data = file_get_contents('php://input');
        $this->data = json_decode($data, true); 
    }

    protected function getApiParams($shop){
        $api  = config()['shopify']['api'][$shop]; // <----- array es seteado por la libreria Shopify
        return $api;
    }

    /*
        No funcionaba (verificar nuevamente)

        https://community.shopify.com/c/Shopify-Apps/product-delete-webhook-not-working/td-p/574094/highlight/false
    */
    function shopify_webhook_products_delete(){       
        // tomando la data de $this->data
        // registrar y/o borrar producto en base de datos que Shopify indica se ha borrado 
    }

    function shopify_webhook_products_create(){
        // tomando la data de $this->data
        // registrar y/o crear producto en base de datos que Shopify indica se ha creado 
    }
    
    function shopify_webhook_products_update(){
        // tomando la data de $this->data
        // registrar y/o actualizar producto en base de datos que Shopify indica se ha actualizado
    }

    /*
        Recibe un objeto que permite persistir los datos

        El objeto podria ser un adaptador sobre la clase Products de WooCommerce por ejemplo
    */
    function shopify_webhook_insert_or_update_products(object $persistent_object){
        $config = config();
    
        if ($config['debug']){
            Logger::log("Shopify WebHook fired");
        }
    
        $data = file_get_contents('php://input');  
        $arr  = json_decode($data, true);  
       
        $headers  = apache_request_headers();
    
        $shop_url = $headers['X-SHOPIFY-SHOP-DOMAIN'] ?? null;
    
        if (!Strings::endsWith('.myshopify.com', $shop_url)){
            Logger::log("WebHook reporta url de tienda de Shopify inválida $shop_url");
            return;
        }
    
        $shop = substr($shop_url, 0, strlen($shop_url) - strlen('.myshopify.com'));
        
        $api  = $this->getApiParams($shop);
    
        $sh   = new Shopify();

        //
        // Independientemente de donde se utilice es buena idea estandarizar la estructura de los datos
        //
        $rows = $sh->adaptToWooCommerce($arr, $api['shop'], $api['api_key'], $api['api_secret'], $api['api_ver']);
    
        if (empty($rows)){
            Logger::log("Error al recibir datos para shop $shop");
        }
    
        foreach ($rows as $row){
            if (!isset($row['sku']) || empty($row['sku'])){
                continue;
            } 
    
            // ya fue convertido
            if ($row['status'] != 'publish'  && !$config['insert_unpublished']){
                continue;
            }
    
            $sku = $row['sku'];
    
            $pid = $persistent_object->getProductIdBySku($sku);
        
            if (!empty($pid)){
                $persistent_object->updateProductBySku($row);
            } else {
    
                if (isset($config['status_at_creation']) && $config['status_at_creation'] != null){
                    $row['status'] = $config['status_at_creation'];
                }
    
                $pid = $persistent_object->createProduct($row);
            }
    
            // En caso que hubiera varios proveedores, guardar el proveedor asociado al producto
            $persistent_object->updateVendor($api['slug'], $pid);
        }   
    }
    
    /*
        Dado el nombre de una tienda $shop, registra todos los webhooks en ella
    */
    function shopify_create_webooks(){
        $data = file_get_contents('php://input');
        $arr  = json_decode($data, true);  

        if ($arr == null || $arr['shop'] == null){
            return [];
        }

        $shop = $arr['shop'];

        $api  = $this->getApiParams($shop); 
    
        if (empty($api)){
            // si hay error y no se puede traer las keys aborto
            return [];
        }
        
        $shop       = $api['shop'];
        $api_key    = $api['api_key'];
        $api_secret = $api['api_secret'];
        $api_ver    = $api['api_ver'];    

        $res = [];

        $sh = new Shopify();

        $ok = $sh->createWebhook($shop, 'products', 'create', $api_key, $api_secret, $api_ver);
        $res['weboook_product_create'] = $ok;

        $ok = $sh->createWebhook($shop, 'products', 'update', $api_key, $api_secret, $api_ver);
        $res['weboook_product_update'] = $ok;

        $ok = $sh->createWebhook($shop, 'products', 'delete', $api_key, $api_secret, $api_ver);
        $res['weboook_product_delete'] = $ok;

        return $res;
    }


}


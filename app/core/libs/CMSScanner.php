<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class CMSScanner
{
    protected static function __getSite(string $url){
        $cli = ApiClient::instance($url)
        ->followLocations()
        ->withoutStrictSSL()
        ->cache(3600 * 24 * 31)
        ->userAgent(ApiClient::USER_AG_CHROME)
        ->get();   

        $status  = $cli->getStatus();
        $error   = $cli->getError();
        $headers = $cli->getHeaders();
        $data    = $cli->data();

        if ($status == 0 || $status >= 400 || empty($data)){
            dd($cli->getStatus(), 'STATUS');
            dd($cli->getError(), 'ERROR');
            dd($cli->getHeaders(), 'HEADERS');

            dd($cli->dump(), 'DUMP');
            exit;
        }
    
        $res = $cli->getResponse();

        return $res['data'];
    }

    static function isBuiltWithWordPress($site){
        return Strings::containsAny(['/wp-includes/', '/wp-content/plugins/'], $site);
    }

    static function isBuiltWithMagento($site){
        return Strings::containsAny(['x-magento-init', 'Magento_PageBuilder'], $site);
    }

    static function isBuiltWithShopify($site){
        return Strings::containsAny([
            'shopify-digital-wallet', 'shopify-checkout-api-token', 'Shopify.shop',
            'Shopify.shop', 'Shopify.theme', '.myshopify.com'        
        ], $site);
    }

    static function isPrestaShop($site){
        return Strings::containsAny(['var prestashop =', 'prestashop.on('], $site);
    }

    /*
        
        $url = 'http://woo4.lan';  // WordPress
        $url = 'www.leifshop.com'; // Shopify
        $url = 'https://www.lechocolat-alainducasse.com/'; // PrestaShop
    */
    static function identify(string $url){
        $site = static::__getSite($url);

        $callbacks = [
            'isBuiltWithMagento', 
            'isBuiltWithWordPress', 
            'isBuiltWithShopify', 
            'isBuiltWithPrestaShop'
        ];

        foreach ($callbacks as $cb) {
            // dd("Verificando si $url $cb");

            if (static::$cb($site)){
                return $cb;
            }
        }
    }


}


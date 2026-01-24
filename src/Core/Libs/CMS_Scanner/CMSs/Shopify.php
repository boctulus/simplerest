<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs;

use Boctulus\Simplerest\Core\Libs\Strings;

class Shopify
{
    static function isIt($content){
        return Strings::containsAny([
            'shopify-digital-wallet', 'shopify-checkout-api-token', 
            'Shopify.shop', 'Shopify.theme', '.myshopify.com', 'https://cdn.shopify.com', 'window.Shopify'        
        ], $content);
    }


}


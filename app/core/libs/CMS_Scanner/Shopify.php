<?php

namespace simplerest\core\libs\CMS_Scanner;

use simplerest\core\libs\Strings;

class Shopify
{
    static function isBuiltWithShopify($site){
        return Strings::containsAny([
            'shopify-digital-wallet', 'shopify-checkout-api-token', 'Shopify.shop',
            'Shopify.shop', 'Shopify.theme', '.myshopify.com'        
        ], $site);
    }


}


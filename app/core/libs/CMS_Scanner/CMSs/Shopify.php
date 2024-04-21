<?php

namespace simplerest\core\libs\CMS_Scanner\CMSs;

use simplerest\core\libs\Strings;

class Shopify
{
    static function isIt($site){
        return Strings::containsAny([
            'shopify-digital-wallet', 'shopify-checkout-api-token', 'Shopify.shop',
            'Shopify.shop', 'Shopify.theme', '.myshopify.com'        
        ], $site);
    }


}


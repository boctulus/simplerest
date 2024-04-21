<?php

namespace simplerest\core\libs\CMS_Scanner\CMSs;

use simplerest\core\libs\Strings;

class Squarespace
{
    static function isIt($site){
        return Strings::containsAny(['http://static1.squarespace.com', 'sqs-cart-quantity',
        'sqs-button-element', 'sqs-product-mark-wrapper', 'https://images.squarespace-cdn.com'], $site);
    }


}


<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs;

use Boctulus\Simplerest\Core\Libs\Strings;

class Squarespace
{
    static function isIt($content){
        return Strings::containsAny(['http://static1.squarespace.com', 'sqs-cart-quantity',
        'sqs-button-element', 'sqs-product-mark-wrapper', 'https://images.squarespace-cdn.com'], $content);
    }


}


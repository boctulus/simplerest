<?php

namespace simplerest\core\libs\CMS_Scanner\CMSs;

use simplerest\core\libs\Strings;

class Magento
{
    static function isIt($content){
        return Strings::containsAny(['x-magento-init', 'Magento_PageBuilder'], $content);
    }


}


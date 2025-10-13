<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs;

use Boctulus\Simplerest\Core\Libs\Strings;

class Magento
{
    static function isIt($content){
        return Strings::containsAny(['x-magento-init', 'Magento_PageBuilder'], $content);
    }


}


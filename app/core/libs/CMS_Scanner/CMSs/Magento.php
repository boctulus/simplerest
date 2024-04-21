<?php

namespace simplerest\core\libs\CMS_Scanner\CMSs;

use simplerest\core\libs\Strings;

class Magento
{
    static function isIt($site){
        return Strings::containsAny(['x-magento-init', 'Magento_PageBuilder'], $site);
    }


}


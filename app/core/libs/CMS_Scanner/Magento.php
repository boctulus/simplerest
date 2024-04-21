<?php

namespace simplerest\core\libs\CMS_Scanner;

use simplerest\core\libs\Strings;

class Magento
{
    static function isBuiltWithMagento($site){
        return Strings::containsAny(['x-magento-init', 'Magento_PageBuilder'], $site);
    }


}


<?php

namespace simplerest\core\libs\CMS_Scanner;

use simplerest\core\libs\Strings;

class PrestaShop
{
    static function isPrestaShop($site){
        return Strings::containsAny(['var prestashop =', 'prestashop.on('], $site);
    }


}


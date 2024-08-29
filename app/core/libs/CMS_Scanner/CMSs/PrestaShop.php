<?php

namespace simplerest\core\libs\CMS_Scanner\CMSs;

use simplerest\core\libs\Strings;

class PrestaShop
{
    static function isIt($content){
        return Strings::containsAny(['var prestashop =', 'prestashop.on('], $content);
    }


}


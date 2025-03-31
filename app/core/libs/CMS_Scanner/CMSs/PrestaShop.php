<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs;

use Boctulus\Simplerest\Core\Libs\Strings;

class PrestaShop
{
    static function isIt($content){
        return Strings::containsAny(['var prestashop =', 'prestashop.on('], $content);
    }


}


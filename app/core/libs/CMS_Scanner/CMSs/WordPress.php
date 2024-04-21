<?php

namespace simplerest\core\libs\CMS_Scanner\CMSs;

use simplerest\core\libs\Strings;

class WordPress
{
    static function isIt($site){
        return Strings::containsAny(['/wp-includes/', '/wp-content/plugins/'], $site);
    }
}


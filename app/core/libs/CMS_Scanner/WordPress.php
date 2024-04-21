<?php

namespace simplerest\core\libs\CMS_Scanner;

use simplerest\core\libs\Strings;

class WordPress
{
    static function isBuiltWithWordPress($site){
        return Strings::containsAny(['/wp-includes/', '/wp-content/plugins/'], $site);
    }
}


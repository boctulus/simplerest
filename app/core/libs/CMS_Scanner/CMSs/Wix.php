<?php

namespace simplerest\core\libs\CMS_Scanner\CMSs;

use simplerest\core\libs\Strings;

class Wix
{
    static function isIt($site){
        return Strings::containsAny(['<meta name="generator" content="Wix.com', 'https://static.wixstatic.com/', 'WixSite:', '.wix.com', 'wix-essential-viewer-model'], $site);
    }
}


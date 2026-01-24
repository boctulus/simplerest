<?php

namespace Boctulus\Simplerest\Core\Libs\CMS_Scanner\CMSs;

use Boctulus\Simplerest\Core\Libs\Strings;

class Wix
{
    static function isIt($content){
        return Strings::containsAny(['<meta name="generator" content="Wix.com', 'https://static.wixstatic.com/', 'WixSite:', '.wix.com', 'wix-essential-viewer-model'], $content);
    }
}


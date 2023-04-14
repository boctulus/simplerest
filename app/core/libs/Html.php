<?php

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

class HTML
{
    /*
        https://stackoverflow.com/a/7131156/980631
    */
    static function stripTagScript(string $html) {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $script = $dom->getElementsByTagName('script');

        $remove = [];
        foreach($script as $item){
            $remove[] = $item;
        }

        foreach ($remove as $item){
            $item->parentNode->removeChild($item); 
        }

        $html = $dom->saveHTML();
        
        return $html;
    }

    /*
        https://davidwalsh.name/remove-html-comments-php
    */
    static function removeComments(string $html = '') {
        return preg_replace('/<!--(.|\s)*?-->/', '', $html);
    }
}


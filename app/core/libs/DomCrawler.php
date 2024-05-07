<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;
use Symfony\Component\DomCrawler\Crawler;

class DomCrawler extends Crawler
{
    /*
        Obtiene el nodo de texto o el atributo

        Util "en general"
    */
    function get($selector, $attr_name = '')
    {
        if (!empty($attr_name)){
            return $this->filter($selector)->attr($attr_name);
        } else{
            return $this->filter($selector)->text();
        }        
    }
}


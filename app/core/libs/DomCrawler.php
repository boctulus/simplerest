<?php

namespace simplerest\core\libs;

use Symfony\Component\DomCrawler\Crawler;

/*
    La idea de esta clase es generar una interfaz comun
    con Pyhton Selenium
*/
class DomCrawler extends Crawler
{   
    function get($selector)
    {
        return $this->filter($selector);        
    }

    function getAttr(string $selector, string $attr_name)
    {
       return $this->filter($selector)->attr($attr_name);
    }

    function getText(string $selector)
    {
        return $this->filter($selector)->text();
    }
}


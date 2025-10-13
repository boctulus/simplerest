<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\JsonLd;
use Boctulus\Simplerest\Libs\Scrapers\GiglioScraper;

class JsonldController extends Controller
{       
    /*
        url='https://www.giglio.com/scarpe-uomo_sneakers-alexander-mcqueen-586198whx52.html?cSel=002'
    */
    function run()
    {
        $url  = $_GET['url']; 

        GiglioScraper::setExpTime(600000);
        dd(GiglioScraper::getProduct($url));                 
    }

    function test(){
        $url  = $_GET['url']; 

        GiglioScraper::setExpTime(600000);
        $html = GiglioScraper::getHTML($url);

        dd(GiglioScraper::getProductLinks($html)); 
    }
}


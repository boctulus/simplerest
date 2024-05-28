<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\core\controllers\Controller;
use simplerest\core\libs\JsonLd;
use simplerest\libs\scrapers\GiglioScraper;

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


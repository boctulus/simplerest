<?php

namespace simplerest\controllers;

use simplerest\core\libs\Strings;
use simplerest\core\libs\CSS;
use simplerest\controllers\MyController;

class CssExtractorController extends MyController
{
    function parse()
    {
        // $url = 'https://genie.warehouserack.com/#/pallet-rack/new/multiple-rows/144x36x96&levels=2&length=57&width=23&aisle=156&usesupport=false&usewiredeck=true';
        $url = 'D:\www\simplerest\etc\pallet_rack\fullpage.html';
        
        dd(
            CSS::extractStyleUrls($url, true)
        , "Assets para $url");

    }

    function download()
    {
        // $url = 'https://genie.warehouserack.com/#/pallet-rack/new/multiple-rows/144x36x96&levels=2&length=57&width=23&aisle=156&usesupport=false&usewiredeck=true';
        $url = 'D:\www\simplerest\etc\pallet_rack\fullpage.html';
        
        dd(
            CSS::downloadAll($url, true)
        , "Assets para $url");
       
    }
}


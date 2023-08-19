<?php

namespace simplerest\controllers;

use simplerest\core\libs\Strings;
use simplerest\core\libs\CSS;
use simplerest\core\libs\Files;
use simplerest\core\libs\Url;
use simplerest\controllers\MyController;

class CssExtractorController extends MyController
{
    function parse()
    {
        $url = 'D:\www\simplerest\etc\practicatest\1.html';
        
        dd(
            CSS::downloadAll($url)
        );
       
    }
}


<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\libs\XML;
use simplerest\core\Response;
use simplerest\core\libs\Files;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class CssExtractorController extends MyController
{
    function parse()
    {
        $html = Files::getContent('D:\www\simplerest\etc\practicatest\1.html');

        dd(
            XML::extractLinkUrls($html, ['css', 'json'])
        );
    }
}


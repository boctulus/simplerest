<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\libs\CSS;
use simplerest\core\Response;
use simplerest\core\libs\Files;
use simplerest\core\libs\Url;
use simplerest\controllers\MyController;

class CssExtractorController extends MyController
{
    function parse()
    {
        /*
            La idea es:

            - Descargar cada archivo .css    -- ok
            - Generar una linea con css_file() para cada uno
            - Permitir excepciones o en algunos casos hacerlas de forma automatica como con Google Fonts

            https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap
        */

        // provendria de otra funcion

        $urls = [
            'https://practicatest.cl/dist/css/basic.min.css',
            'https://practicatest.cl/dist/css/style.themed.css?v=1'
        ];

        foreach ($urls as $url){
            $domain = Url::getDomain($url);
            $path   = ASSETS_PATH . $domain;

            Files::mkDirOrFail($path);        
            Files::download($url, $path);
        }

        // $html = Files::getContent('D:\www\simplerest\etc\practicatest\1.html');

        // dd(
        //     CSS::extractLinkUrls($html, true)
        // );
    }
}


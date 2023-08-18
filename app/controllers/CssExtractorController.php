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

            - Descargar cada archivo .css
            - Generar una linea con css_file() para cada uno
            - Traer los parametros query como "?family=Roboto:wght@400&display=swap" y pero opcionalmente no hacelro
            - Permitir excepciones o en algunos casos hacerlas de forma automatica como con Google Fonts

            https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap
        */

        $domain = Url::getDomain('https://practicatest.cl/static/fonts/css/light.css');
        $path   = ASSETS_PATH . $domain;

        Files::mkDirOrFail($path);

        dd($path);
        exit;

        $html = Files::getContent('D:\www\simplerest\etc\practicatest\1.html');

        dd(
            CSS::extractLinkUrls($html, true)
        );
    }
}


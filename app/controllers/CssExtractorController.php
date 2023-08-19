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
         $html = Files::getContent('D:\www\simplerest\etc\practicatest\1.html');

        dd(
            CSS::extractLinkUrls($html, true, true)
        );

        exit;

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

        $filenames = [];
        foreach ($urls as $url){
            $domain = Url::getDomain($url);
            $path   = ASSETS_PATH . $domain;

            // Files::mkDirOrFail($path);        
            // $bytes = Files::download($url, $path);   

            // if (empty($bytes)){
            //     throw new \Exception("Download '$url' was not possible");
            // }

            $filename    = Files::getFilenameFromURL($url);
            $filenames[] = $path . DIRECTORY_SEPARATOR . $filename;
            
            // "css_file('$file');"; 
        }

        $out = '';
        foreach ($filenames as $ix => $filename){
            $filenames[$ix] = Strings::diff($filename, ASSETS_PATH);
            $out .= PHP_EOL . "css_file('$filenames[$ix]');";
        }

        dd($out);

       
    }
}


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
        /*           
            - Descargar cada archivo .css
            - Generar una linea con css_file() para cada uno
        */

        $html = Files::getContent('D:\www\simplerest\etc\practicatest\1.html');

        $urls = CSS::extractLinkUrls($html, true);

        $filenames = [];
        foreach ($urls as $url){
            $domain = Url::getDomain($url);
            $path   = ASSETS_PATH . $domain;

            Files::mkDirOrFail($path);        
            $bytes = Files::download($url, $path);   

            if (empty($bytes)){
                throw new \Exception("Download '$url' was not possible");
            }

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


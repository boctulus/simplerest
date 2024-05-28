<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Files;

class ImgController extends Controller
{
    function mv($ori = 'D:\Downloads', $dst = null)
    {
        $jpg = array_merge(
            Files::glob($ori, '*.jpg'),
            Files::glob($ori, '*.jpeg')
        );
        
        $png = Files::glob($ori, '*.png');

        if (empty($dst)){
            $dst = $ori . DIRECTORY_SEPARATOR;
        }        

        $dst = Files::addTrailingSlash($dst);

        dd($dst, "DESTINATION");

        foreach  ($png as $path)
        {            
            $filename = Strings::after($path, $ori . DIRECTORY_SEPARATOR);

            if (Strings::startsWith($ori . DIRECTORY_SEPARATOR .'Screenshot_', $filename)){
                $new_name = time() . '-' . Strings::randomHexaString(10) . '.png';
            } else {
                $new_name = time() . '-' . $filename;  
            }
            
            dd("$path -> ". "$dst{$new_name}");
            rename($path, "$dst{$new_name}");
        }

        foreach  ($jpg as $path)
        {   
            $filename = Strings::after($path, $ori . DIRECTORY_SEPARATOR);

            $new_name = time() . '-' . $filename;

            dd("$path -> ". "$dst{$new_name}");
            rename($path, "$dst{$new_name}");
        }
    }

}


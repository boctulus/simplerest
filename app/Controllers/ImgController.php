<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Files;

class ImgController extends Controller
{
    function mv($ori = 'D:\Downloads', $dst = null)
    {
        // HARDCODED
        $dst = 'D:\Downloads\0';

        $jpg = array_merge(
            Files::glob($ori, '*.jpg'),
            Files::glob($ori, '*.jpeg'),
            Files::glob($ori, '*.webp')
        );
        
        $png = Files::glob($ori, '*.png');
        $mp4 = Files::glob($ori, '*.mp4');

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

        foreach  ($mp4 as $path)
        {            
            $filename = Strings::after($path, $ori . DIRECTORY_SEPARATOR);

            if (Strings::startsWith($ori . DIRECTORY_SEPARATOR .'Screenshot_', $filename)){
                $new_name = time() . '-' . Strings::randomHexaString(10) . '.mp4';
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


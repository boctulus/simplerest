<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\FileCache;
use simplerest\core\interfaces\IMemoization;

class FileMemoization implements IMemoization
{
    /*
        Memoriza el resultado de un callback y devuelve el valor hasta antes de expiracion

        Cuando haya expirado mismo callback para recalcular y cachear el nuevo valor

        El archivo no se serializa    
    
        Ej:

        $git_installed = FileMemoization::memoize('git exists', function() {
             return System::inPATH('git') ? 1 : 0;
        }, 3600 * 24 );
    */
    static function memoize($key, $callback = null, $expiration_time = null) 
    {
        $key = md5($key);

        $filename = FileCache::getCachePath($key);

        if (file_exists($filename) && !FileCache::expired(filemtime($filename), $expiration_time)){
            return file_get_contents($filename);
        }

        if ($callback != null && is_callable($callback)){
            $value = $callback();
        } 

        file_put_contents($filename, $value);

        return $value;
    }


}


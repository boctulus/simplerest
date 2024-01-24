<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\FileCache;
use simplerest\core\interfaces\IMemoization;

class FileMemoizationV2 implements IMemoization
{
    /*
        Memoriza el resultado de un callback y devuelve el valor hasta antes de expiracion

        Cuando haya expirado mismo callback para recalcular y cachear el nuevo valor

        -> El archivo es serializado <-       

        Ej:

        $git_log = FileMemoizationV2::memoize('git log', function() {
            return System::execAtRoot("git log");
        }, 3600 );
    */
    static function memoize($key, $callback_or_value = null, $expiration_time = null) 
    {
        $key = md5($key);

        $value = FileCache::get($key, null);

        if ($value !== null){
            return $value;
        }

        if ($callback_or_value != null && is_callable($callback_or_value)){
            $value = $callback_or_value();
        } else {
            $value = $callback_or_value;
        }

        FileCache::put($key, $value, $expiration_time);

        return $value;
    }


}


<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\FileCache;
use simplerest\core\interfaces\IMemoization;

class FileMemoization implements IMemoization
{
    static function memoize($key, $callback_or_value = null, $expiration_time = null) 
    {
        $key = md5($key);

        if ($callback_or_value != null && is_callable($callback_or_value)){
            $value = $callback_or_value();
        } else {
            $value = $callback_or_value;
        }

        // Si se proporciona $value, asigna ese valor al caché y retorna el valor
        if ($value !== null) {
            FileCache::put($key, $value, $expiration_time);
            return $value;
        }

        return FileCache::get($key);
    }


}


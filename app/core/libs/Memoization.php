<?php

namespace simplerest\core\libs;

/*
    Implementacion SIN persistencia
    
    TO-DO

    - Convertir a static en un driver mas (StaticCache)

    - Hacer que esta clase Memorization utilice los distintos drivers

    $driver = config('cache_driver');
*/
class Memoization
{
    protected static $cache = [];

    static function memoize($key, $callback_or_value = null)
    {
        if ($callback_or_value != null && is_callable($callback_or_value)){
            $value = $callback_or_value();
        } else {
            $value = $callback_or_value;
        }
        
        // Si se proporciona $value, asigna ese valor al caché y retorna el valor
        if ($value !== null) {
            static::$cache[$key] = $value;
            return $value;
        }

        return static::$cache[$key] ?? null;
    }
}


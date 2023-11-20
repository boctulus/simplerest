<?php

namespace simplerest\core\traits;

trait MemoizationTrait
{
    protected static $cache = [];

    static function memoize($key, $value = null)
    {
        // Si se proporciona $value, asigna ese valor al caché y retorna el valor
        if ($value !== null) {
            static::$cache[$key] = $value;
            return $value;
        }

        // Si no se proporciona $value y la clave existe en el caché, retorna el valor almacenado
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        // Si no se proporciona $value y la clave no existe en el caché, retorna null
        return null;
    }
}

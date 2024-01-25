<?php

namespace simplerest\core\libs;

use simplerest\core\interfaces\ICache;

/*
    Cache que expira cuando termina el request
    y tiene como scope solo el request
*/
class InMemoryCache implements ICache
{
    protected static $cache = [];

    // Never expires
    static function expired($cached_at, int $expiration_time) : bool {
        return false;
    }

    static function put(string $key, $value, int $exp_time = -1) {
        static::$cache[$key] = $value;
    }

    static function get(string $key){
        return static::$cache[$key] ?? null;
    }

    static function forget(string $key){
        unset(static::$cache[$key]);
    }
}


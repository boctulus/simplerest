<?php

namespace simplerest\core\libs;

use simplerest\core\interfaces\ICache;

/*
    Idealmente implementar PSR 6 cache interface

    https://www.php-fig.org/psr/psr-6/
*/
abstract class Cache implements ICache
{
    const NEVER   = -1;
    const EXPIRED =  0;

    /*
        Logica para saber si un recurso ha expirado

        @param int|false $cached_at
        @param int       expiration_time es el delta en segundos
    */
    static function expired($cached_at, int $expiration_time) : bool {
        if (!is_int($cached_at) && $cached_at !== false){
            throw new \InvalidArgumentException("cached_at should be int|false but ". gettype($cached_at) . " was received");
        }

        if ($cached_at === false){
            return false;
        }

        if ($expiration_time === 0){
            return true;
        }

        if ($expiration_time == static::NEVER){
            return false;
        }

        return time() > $cached_at + $expiration_time;
    }

   

    abstract static function put(string $key, $value, int $time = -1);
    abstract static function get(string $key, $default = null);
    abstract static function forget(string $key);


}


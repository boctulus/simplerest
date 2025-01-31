<?php

namespace Boctulus\ApiClient\Helpers;

use Boctulus\ApiClient\Interfaces\ICache;

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
        @param int       TTL (tiempo de vida del recurso)
    */
    static function expired($cached_at, int $ttl) : bool {
        if (!is_int($cached_at) && $cached_at !== false){
            throw new \InvalidArgumentException("cached_at should be int|false but ". gettype($cached_at) . " was received");
        }

        if ($cached_at === false){
            return false;
        }

        if ($ttl === 0){
            return true;
        }

        if ($ttl == static::NEVER){
            return false;
        }

        return time() > $cached_at + $ttl;
    }

   

    abstract static function put(string $key, $value, int $time = -1);
    abstract static function get(string $key, $default = null);
    abstract static function forget(string $key);


}


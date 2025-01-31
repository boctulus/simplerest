<?php

namespace Boctulus\ApiClient\Interfaces;

interface ICache {
    static function expired($cached_at, int $expiration_time) : bool;
    static function put(string $key, $value, int $time = -1);
    static function get(string $key);
    static function forget(string $key);
}

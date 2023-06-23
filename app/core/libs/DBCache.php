<?php

namespace simplerest\core\libs;

use simplerest\core\libs\DB;

class DBCache extends Cache
{
     /**
     * Store data in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration_time
     * @return bool
     */
    static function put(string $key, $value, int $expiration_time): bool
    {
        $data = [
            'the_key'   => $key,
            'the_value' => serialize($value),
            'cached_at' => time(),
            'expiration_time' => $expiration_time,
        ];

        dd($data, 'DATA for put()');  // <------------- revisaR

        return DB::table('cache')
        ->create($data);
    }

    /**
     * Retrieve data from the cache.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    static function get(string $key, $default = null)
    {
        $cache = DB::table('cache')
        ->where('the_key', $key)
        ->first();

        if ($cache) {
            if (!self::expired($cache->cached_at, $cache->expiration_time)) {
                return unserialize($cache->the_value)['the_value'];
            }

            // Cache has expired, delete it
            self::forget($key);
        }

        return $default;
    }

    /**
     * Remove data from the cache.
     *
     * @param string $key
     * @return bool
     */
    static function forget(string $key): bool
    {
        return DB::table('cache')->where('the_key', $key)->delete();
    }


}


<?php

namespace simplerest\core\libs;

use simplerest\core\interfaces\ICache;
use simplerest\core\libs\DB;

class DBCache implements ICache
{
     /**
     * Store data in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int $exp_time
     * @return bool
     */
    static function put(string $key, $value, int $exp_time): bool
    {
        $expires_at = time() + $exp_time;

        $data = [
            '_key_'      => $key,
            'value'      => serialize($value),
            'cached_at'  => time(),
            'expires_at' => $expires_at,
        ];

        // dd($data, 'DATA for put()'); 

        return table('cache')
        ->create($data);
    }

    /**
     * Retrieve data from the cache.
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    static function get($key, $default = null)
    {
        $cache = table('cache')
        ->where(['_key_' => $key])
        ->first();

        if ($cache) {
            if ($cache['expires_at'] >= time()) {
                return unserialize($cache['value']);
            }

            // dd("EXPIRED -> CLEANING...");

            // Cache has expired, delete it
            self::forget($key);
        }

        // dd("NON-CACHED -> RETURNING DEFAULT...");

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
        return DB::table('cache')->where('_key_', $key)->delete();
    }

    static function expired($cached_at, int $exp_time) : bool {
        return $cached_at < time();
    }
}


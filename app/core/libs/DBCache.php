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
    static function put(string $key, $value, $exp_time = -1): bool
    {
        $expires_at = time() + $exp_time;

        $data = [
            '_key_'      => $key,
            'value'      => serialize($value),
            'cached_at'  => time(),
            'expires_at' => $expires_at,
        ];

        if (table('cache')->where(['_key_' => $key])){
            return table('cache')
            ->where(['_key_' => $key])
            ->update($data);
        }

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
        return DB::table('cache')->where('_key_', $key)->delete();
    }

    static function expired($cached_at, int $exp_time) : bool {
        return $cached_at < time();
    }
}


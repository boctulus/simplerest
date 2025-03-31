<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Interfaces\ICache;
use Boctulus\Simplerest\Core\Libs\Cache;
use Boctulus\Simplerest\Core\Libs\DB;

class DBCache extends Cache
{
     /**
     * Store data in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    static function put(string $key, $value, $ttl = -1): bool
    {
        $expires_at = time() + $ttl;

        $data = [
            '_key_'      => $key,
            'value'      => serialize($value),
            'cached_at'  => time(),
            'expires_at' => $expires_at,
        ];

        if (table('cache')->where(['_key_' => $key])->exists()){
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
            // Not expired
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

    static function clear(): bool
    {
        return DB::table('cache')->delete();
    }
}


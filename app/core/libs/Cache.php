<?php

namespace simplerest\core\libs;

class Cache
{
    const FOREVER = -1;
    const EXPIRED =  0;

    static function expired(int $cached_at, int $expiration_time) : bool {
        if ($expiration_time == 0){
            return true;
        }

        if ($expiration_time == -1){
            return false;
        }

        return time() > $cached_at + $expiration_time;;
    }

    static function expiredFile(string $cache_path, int $expiration_time) : bool {
        $exists = file_exists($cache_path);

        if (!$exists){
            return true;
        }

        $updated_at = filemtime($cache_path);

        if (static::expired($updated_at, $expiration_time)){
            return true;
        }

        return false;
    }

}


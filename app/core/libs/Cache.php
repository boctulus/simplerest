<?php

namespace simplerest\core\libs;

class Cache
{
    const FOREVER = -1;
    const EXPIRED =  0;

    static function getCachePath(string $url) : string {
        static $path;

        if (isset($path[$url])){
            return $path[$url];
        }

        $filename = str_replace(['%'], ['p'], urlencode(Url::normalize($url))) . '.php';
        $filename = str_replace('/', '', $filename);

        // Evito problemas con nombres largos
        if (strlen($filename) > 250){
            return null;
        }

        $path[$url] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        return $path[$url];
    }

    /*
        Logica para saber si un recurso ha expirado
    */
    static function expired(int $cached_at, int $expiration_time) : bool {
        if ($expiration_time == 0){
            return true;
        }

        if ($expiration_time == -1){
            return false;
        }

        return time() > $cached_at + $expiration_time;;
    }

    /*
        Logica para saber si un archivo usado como cache ha expirado
    */
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


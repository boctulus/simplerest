<?php

namespace Boctulus\ApiClient;

use Boctulus\ApiClient\Helpers\Files;

class FileCache extends Boctulus\ApiClient\Helpers\Cache
{
    /*
        La $key puede ser una url o el nombre de un archivo
    */
    static function getCachePath(string $key) : string {
        static $path;

        if (isset($path[$key])){
            return $path[$key];
        }

        $filename = sha1($key);

        $path[$key] = Files::tempDir() . DIRECTORY_SEPARATOR . $filename . '.cache';
        return $path[$key];
    }

    /*
        Logica para saber si un archivo usado como cache ha expirado

        Si se ha utilizado put() debe usarse con $was_serialized = 1
    */
    static function expiredFile(string $path, $exp_time = null, bool $was_serialized = false) : bool 
    {
        $exists = file_exists($path);

        if (!$exists){
            return true;
        }

        if ($exp_time == Cache::NEVER){
            return false;
        }

        if (!$was_serialized){
            if ($exp_time !== null){
                $updated_at = filemtime($path);

                if (static::expired($updated_at, $exp_time)){
                    // Cache has expired, delete the file only if it is not locked
                    if (Files::reader($path, 'r', 1, LOCK_SH | LOCK_NB)) {
                        unlink($path);
                    }
                
                    return true;
                }
            }
            
            return false;
        }

        $content = Files::reader($path);
        $data    = unserialize($content);

        if ($data['expires_at'] < time()) {
            // Cache has expired, delete the file only if it is not locked
            if (Files::reader($path, 'r', 1, LOCK_SH | LOCK_NB)) {
                unlink($path);
            }

            return true;
        }

        return false;
    }


    static function expiredFileByKey($key, $exp_time = null, bool $was_serialized = false){
        $path = static::getCachePath($key);  

        return static::expiredFile($path, $exp_time, $was_serialized);
    }

    /*
        @param string $key
        @param mixed  $value    
        @param int    $exp_time en segundos
    */
    static function put($key, $value, $exp_time  = -1)
    {
        $path      = static::getCachePath($key);

        $exp_time  = $exp_time ?? 3600 * 365;
        $expiresAt = time() + ($exp_time);

        $data = [
            'value' => $value,
            'expires_at' => $expiresAt,
        ];
        
        $content = serialize($data);

        if (Files::writter($path, $content) !== false) {
            return true;
        }

        return false;
    }

    static function get($key, $default = null)
    {
        $path = static::getCachePath($key);

        if (!file_exists($path)) {
            return $default;
        }

        $content = Files::reader($path);
        $data    = unserialize($content);

        if ($data['expires_at'] < time()) {
            // Cache has expired, delete the file
            unlink($path);
            return $default;
        }

        return $data['value'];
    }

    static function forget($key)
    {
        $path = static::getCachePath($key);

        if (!file_exists($path)) {
            return;
        }
        
        unlink($path);
    }
}


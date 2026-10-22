<?php

use Boctulus\Simplerest\Core\Libs\Env;

if (!function_exists('env')){
    /**
     * Return a process environment value when present, then fall back to the loaded env file.
     * An explicitly empty process value is still considered present.
     */
    function env(string $key, $default_value = null){
        $value = getenv($key);

        if ($value !== false){
            return $value;
        }

        return Env::get($key, $default_value);
    }
}

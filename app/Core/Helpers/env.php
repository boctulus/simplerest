<?php

use Boctulus\Simplerest\Core\Libs\Env;

if (!function_exists('env')){
    function env(string $key, $default_value = null){
        return Env::get($key, $default_value);
    }
}

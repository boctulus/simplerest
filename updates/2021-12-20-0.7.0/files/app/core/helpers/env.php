<?php

use simplerest\core\libs\Env;

if (!function_exists('env')){
    function env(string $key){
        return Env::get($key);
    }
}

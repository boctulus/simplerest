<?php

use simplerest\core\libs\Strings;
use simplerest\core\libs\Config;
use simplerest\core\Model;
use simplerest\core\libs\Factory;

if (!function_exists('env')){
    function env(string $key, $default_value = null){
        return $_ENV[$key] ?? $default_value;
    }
}

function config(){
    return Config::get();
}



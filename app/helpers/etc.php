<?php

use simplerest\libs\Strings;
use simplerest\libs\Config;
use simplerest\core\Model;
use simplerest\libs\Factory;

// if (!function_exists('env')){
//     function env(string $key, $default_value = null){
//         return $_ENV[$key] ?? $default_value;
//     }
// }

function config(){
    return Config::get();
}

function puff(){
    throw new \Exception("PUFF");
}



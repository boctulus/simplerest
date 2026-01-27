<?php

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Strings;

function config($key = null){
    return Config::get($key);
}

/*
    Cuando usa la clase Config, accede al config.php 
    y los valores persisten solo dentro del request.

    Si @key es un archivo .php, devuelve el content
*/

function get_cfg($key) {
    if (Strings::endsWith('.php', $key)){
        return include CONFIG_PATH . $key;
    }

    return Config::get($key);
}

function set_cfg($key, $val) {
    return Config::set($key, $val);
}



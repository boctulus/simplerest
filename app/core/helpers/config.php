<?php

use simplerest\core\libs\Config;

function config($key = null){
    return Config::get($key);
}


/*
    Cuando usa la clase Config, accede al config.php 
    y los valores los escribe en runtime pero se persisten
*/

function get_cfg($key) {
    return Config::get($key);
}

function set_cfg($key, $val) {
    return Config::set($key, $val);
}

<?php

use simplerest\core\libs\Config;

/*
    Implementacion de transientes

    Lo ideal es seguir la prioridad:

    REDIS o MEMCACHED > DB > FILES

    El driver usado es configurable via config.php 
*/

function set_transient(string $key, $value, $exp_time = null){
    $driver = config('cache_driver');
    return $driver::put($key, $value, $exp_time);
}

function get_transient(string $key, $default = null){
    $driver = config('cache_driver');
    return $driver::get($key, $default);
}

function delete_transient(string $key){
    $driver = config('cache_driver');
    $driver::forget($key);
}

function set_cache_driver($class){
    Config::set('cache_driver', $class);
}
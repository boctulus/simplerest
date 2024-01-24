<?php

/*
    Implementacion de transientes

    Lo ideal es seguir la prioridad:

    REDIS o MEMCACHED > DB > FILES

    El driver usado es configurable via config.php 
*/

function set_transient(string $key, $value, $exp_time = null){
    $driver = config('default_driver');

    return $driver::put($key, $value, $exp_time);
}

function get_transient(string $key, $default = null){
    $driver = config('default_driver');

    return $driver::get($key, $default);
}

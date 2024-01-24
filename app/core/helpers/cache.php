<?php

use simplerest\core\libs\FileCache;

/*
    Implementacion de transientes

    Se establecera una prioridad:

    REDIS o MEMCACHED > DB > FILES

    En principio sera configurable el driver por defecto que sera DB 
*/

function set_transient(string $key, $value, $exp_time = null){
    return FileCache::put($key, $value, $exp_time);
}

function get_transient(string $key, $default = null){
    return FileCache::get($key, $default);
}

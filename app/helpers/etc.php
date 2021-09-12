<?php

if (!function_exists('env')){
    function env(string $key, $default_value = null){
        return $_ENV[$key] ?? $default_value;
    }
}

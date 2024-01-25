<?php

namespace simplerest\core\libs;

class Memoization
{
    static function memoize($key, $callback = null, $expiration_time = null) 
    {
        $driver = config('cache_driver');

        $key    = md5($key);
        $value  = $driver::get($key, null);

        if ($value !== null){
            return $value;
        }

        if ($callback != null && is_callable($callback)){
            $value = $callback();
        } 

        $driver::put($key, $value, $expiration_time);

        return $value;
    }

}


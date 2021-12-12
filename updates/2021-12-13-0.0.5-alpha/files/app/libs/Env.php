<?php

declare(strict_types=1);

namespace simplerest\libs;

class Env
{
    static $data;

    static function setup(){
        if (!empty($_ENV)){
            static::$data = $_ENV;  
        }

        static::$data = parse_ini_file(ROOT_PATH . '.env');
    }

    static function get(?string $key = null){
        if (empty(static::$data)){
            static::setup();
        }

        if (empty($key)){
            return static::$data;
        } 

        return static::$data[$key];
    }
}


<?php

namespace simplerest\libs;

class Config
{
    static protected $data = [];

    static protected function setup()
    {   
        static::$data = include CONFIG_PATH . 'config.php';
    }

    static function get(?string $property = null)
    {
        if (empty(static::$data)) {
            static::setup();
        }

        if ($property === null) {
            return static::$data;
        }

        return static::$data[$property];
    }

    static function set(string $property, $value){
        static::$data[$property] = $value;
    }
}

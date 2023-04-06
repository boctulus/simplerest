<?php declare(strict_types=1);

namespace simplerest\core\libs;;

class Env
{
    static $data;

    static function setup(){
        if (!file_exists(ROOT_PATH . '.env')){
            throw new \Exception("File .env not found in path " . ROOT_PATH . '.env');
        }
        
        if (!empty($_ENV)){
            static::$data = $_ENV;  
        }

        static::$data = parse_ini_file(ROOT_PATH . '.env');
    }

    static function get(?string $key = null, $default_value = null){
        if (empty(static::$data)){
            static::setup();
        }

        if (empty($key)){
            return static::$data;
        } 

        return static::$data[$key] ?? $default_value;
    }
}


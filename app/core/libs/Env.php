<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class Env
{
    static $data;

    static function setup(){
        if (!file_exists(ROOT_PATH . '.env')){
            if (!file_exists(ROOT_PATH . '/.env')){
                if (!file_exists(ROOT_PATH . '/env.example')){
                    throw new \Exception("Neither .env nor env.example found");
                }

                copy(ROOT_PATH . 'env.example', ROOT_PATH . '.env');
            }
        }
        
        if (!empty($_ENV)){
            static::$data = $_ENV;  
        }

        // Doy prioridad a '.dev-env' sobre '.env'
        $env_file     = file_exists(ROOT_PATH . '.dev-env') && trim(file_get_contents(ROOT_PATH . '.dev-env')) !='' ? '.dev-env' : '.env';

        static::$data = parse_ini_file(ROOT_PATH . $env_file);

        if (static::$data === false){
            $ini_file = ROOT_PATH . '.env';

            throw new \Exception("Invalid .ini file. Syntax error in \"$ini_file\"");
        }

    }

    static function set(string $key = null, $value){
        if (static::$data === null){
            static::setup();
        }
        
        static::$data[$key] = $value;
    }

    static function get(?string $key = null, $default_value = null){
        if (static::$data === null){
            static::setup();
        }

        if (empty($key)){
            return static::$data;
        } 

        return static::$data[$key] ?? $default_value;
    }

    /**
     * Retrieves a boolean value from the environment configuration.
     *
     * @param string $key The environment key to fetch.
     * @param bool $default_value Default value to return if the key is not found or invalid.
     * @return bool Parsed boolean value.
     */
    static function getBool(string $key, bool $default_value = false): bool {
        if (static::$data === null) {
            static::setup();
        }

        $value = static::$data[$key] ?? $default_value;

        // Use Strings::parseOption to convert to boolean
        return Strings::parseOption($value, $default_value);
    }
}


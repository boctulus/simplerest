<?php declare(strict_types=1);

namespace simplerest\core\libs;

/*
    Acepta sintaxis "dot" 

    Ej:

    Config::set('db_connections.main.tb_prefix', 'wp_');

    Se puede hacer un merge a nivel de "root" (/) pasando como key null o '' o '/' o '.'

    Ej:

    Config::set('', include __DIR__ . '/config/config.php');
*/
class Config
{
    static protected $data = [];

    static protected function setup()
    {
        static::$data = array_merge(
            include CONFIG_PATH . 'config.php',
            include CONFIG_PATH . 'databases.php'
        );
    }

    static function get($property = null)
    {
        if (empty(static::$data)) {
            static::setup();
        }

        if ($property === null) {
            return static::$data;
        }

        // Split the property into an array of keys
        $keys = explode('.', $property);
        $value = static::$data;

        // Traverse the nested array to get the final value
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null; // Property not found
            }
        }

        return $value;
    }


    /*
        Acepta sintaxis "dot" 

        Ej:

        Config::set('db_connections.main.tb_prefix', 'wp_');
    */

    static function set(string $property, $value)
    {
        if (empty(static::$data)) {
            static::setup();
        }

        // Check if the property is null or '/'
        if ($property === null || $property === '/' || $property === '.' || $property === '') {
            // Merge the new value with the root array
            static::$data = array_merge(static::$data, $value);
            return;
        }

        // Split the property into an array of keys
        $keys      = explode('.', $property);
        $tempArray = &static::$data;

        // Traverse the nested array to set the final value
        foreach ($keys as $key) {
            if (!isset($tempArray[$key])) {
                $tempArray[$key] = [];
            }
            $tempArray = &$tempArray[$key];
        }

        $tempArray = $value;
    }
    
}

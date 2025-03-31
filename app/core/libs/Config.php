<?php declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Arrays;

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

    static function get($property = null, $default = null)
    {
        if (empty(static::$data)) {
            static::setup();
        }

        if ($property === null) {
            return static::$data;
        }

        return Arrays::get(static::$data, $property, $default);
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

    /*
        Agrega a un nodo de un array nuevos elementos

        El agregado es con key numerica.

        Acepta sintaxis "dot" 

        Ej:

        $config = wp_parse_args($config, [
            'title' => ucfirst($field),
            'field' => $field,            
            'cssClasses' => [],
            'rendererCallback' => $renderer_function
        ]);

        Config::add('datatable_custom_rows', $config);

        TO-DO

        Tercer paramtro opcional $key = null, permitiria
        agregar de forma asociativa. 
        
        Ej:

        // Aun sin implementar
        Config::add('db_connections', $array_conn, 'new_conn');
    */    
    static function add(string $property, $value)
    {
        if (empty(static::$data)) {
            static::setup();
        }

        if ($property === '') {
            if (!is_array(static::$data)) {
                static::$data = [static::$data];
            }
            static::$data[] = $value;
            return;
        }

        $keys = explode('.', $property);
        $lastKey = array_pop($keys);

        $tempArray = &static::$data;

        foreach ($keys as $key) {
            if (!isset($tempArray[$key])) {
                $tempArray[$key] = [];
            }
            $tempArray = &$tempArray[$key];
        }

        if (!isset($tempArray[$lastKey])) {
            $tempArray[$lastKey] = [];
        }

        $target = &$tempArray[$lastKey];

        if (!is_array($target)) {
            $target = [$target];
        }

        $target[] = $value;
    }
}
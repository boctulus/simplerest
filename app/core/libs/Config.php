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

    /**
     * Load and merge package configuration
     *
     * Configuration is stored under: packages.{vendor}.{package}.*
     *
     * @param string $vendor Vendor name (e.g., 'boctulus')
     * @param string $package Package name (e.g., 'zippy')
     * @param array $config Package configuration array
     * @return void
     */
    static function loadPackageConfig(string $vendor, string $package, array $config)
    {
        if (empty(static::$data)) {
            static::setup();
        }

        $namespace = "packages.{$vendor}.{$package}";

        // Store package config in its namespace
        static::set($namespace, $config);
    }

    /**
     * Get configuration from package or fallback to global
     *
     * @param string $vendor Vendor name
     * @param string $package Package name
     * @param string $key Configuration key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    static function getPackageConfig(string $vendor, string $package, string $key, $default = null)
    {
        if (empty(static::$data)) {
            static::setup();
        }

        // Try package-specific config first
        $packageValue = static::get("packages.{$vendor}.{$package}.{$key}");

        if ($packageValue !== null) {
            return $packageValue;
        }

        // Fallback to global config
        return static::get($key, $default);
    }

    /**
     * Extract vendor and package name from controller class name
     *
     * @param string $className Full controller class name
     * @return array|null ['vendor' => string, 'package' => string] or null if not a package
     */
    static function getPackageFromClass(string $className): ?array
    {
        // Expected format: Vendor\Package\Controllers\SomeController
        // or: Boctulus\Zippy\Controllers\ZippyController

        $parts = explode('\\', $className);

        if (count($parts) < 3) {
            return null;
        }

        $rootNamespace = static::get('namespace', 'Boctulus\\Simplerest');

        // Check if it's not the main framework namespace
        if (strpos($className, $rootNamespace) === 0) {
            return null;
        }

        // Extract vendor and package (first two parts)
        $vendor = strtolower($parts[0]);
        $package = strtolower($parts[1]);

        return [
            'vendor' => $vendor,
            'package' => $package
        ];
    }
}
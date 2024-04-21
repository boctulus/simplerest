<?php

declare(strict_types=1);

namespace simplerest\core\libs;

/*
    Reflector

    @author Pablo Bozzolo <boctulus@gmail.com>
*/

class Reflector
{
    static array $info_constructor = [];

    /**
     * Retrieves information about the constructor parameters of a given class.
     * 
     * @param string $class_name The name of the class.
     * @return array|null An array containing information about the constructor parameters, or null if the class has no constructor.
     */
    static function getConstructor(string $class_name)
    {
        if (isset(static::$info_constructor[$class_name])) {
            return static::$info_constructor[$class_name];
        }

        $reflection  = new \ReflectionClass($class_name);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return null; // No hay constructor
        }

        $params = $constructor->getParameters();

        $param_names = [];
        $qty_required = 0;
        $qty_optional = 0;
        $required_params = [];
        $info = [];

        foreach ($params as $param) {
            $param_name    = $param->getName();
            $param_type    = $param->getType();

            $is_optional   = $param->isOptional();
            $is_nullable   = $param->allowsNull();

            $is_def_val_av = $param->isDefaultValueAvailable();

            if ($param->isDefaultValueAvailable()) {
                $def_val = $param->getDefaultValue();
            }

            $is_array    = $param_type ? $param_type->getName() === 'array' : false;
            $is_callable = $param_type ? $param_type->getName() === 'callable' : false;

            $is_passed_by_ref = $param->isPassedByReference();

            $param_names[] = $param_name;

            if (!$is_optional) {
                $qty_required++;
                $required_params[] = $param_name;
            } else {
                $qty_optional++;
            }

            $info[] = [
                'param_name' => $param_name,
                'required'   => !$is_optional,
                'is_array' => $is_array,
                'is_callable' => $is_callable,
                'is_nullable' => $is_nullable,
                'default_value' => $is_def_val_av ? $def_val : null,
                'passed_by_reference' => $is_passed_by_ref
            ];
        }

        static::$info_constructor[$class_name] = [
            'param_names' => $param_names,
            'optional_qty' => $qty_optional,
            'required_qty' => $qty_required,
            'required_params' => $required_params,
            'params' => $info
        ];

        return static::$info_constructor[$class_name];
    }


    /**
     * Retrieves public methods of a class
     * 
     * @param string $class_name The name of the class.
     * @param string $prefix The prefix to filter methods by.
     * @return array An array containing the names of public methods that start with the specified prefix.
     */
    static function getPublicMethods(string $class_name): array
    {
        $reflection = new \ReflectionClass($class_name);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        return $methods;
    }


    /*
     * Retrieves public methods of a class that start with a specific prefix.
     * 
     * @param string $class_name The name of the class.
     * @param string $prefix The prefix to filter methods by.
     * @return array An array containing the names of public methods that start with the specified prefix.
     * 
        Ej:
      
        $site = static::__getSite($url);
    
        // Obtener los métodos públicos que empiezan con "isBuiltWith"
        $callbacks = Reflector::getPublicMethodsStartingWith(__CLASS__, 'isBuiltWith');
    
        foreach ($callbacks as $cb) {
            if (static::$cb($site)){
                return substr($cb, 11); // luego de "isBuiltWith"
            }
        }
     */
    static function getPublicMethodsStartingWith(string $class_name, string $prefix): array
    {
        $methods   = static::getPublicMethods($class_name);
        $callbacks = [];

        foreach ($methods as $method) {
            if (strpos($method->name, $prefix) === 0) {
                $callbacks[] = $method->name;
            }
        }

        return $callbacks;
    }
}

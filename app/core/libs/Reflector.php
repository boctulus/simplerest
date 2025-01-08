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
    static array $class_cache = [];

    /**
     * Obtiene toda la información documentable de una clase
     * 
     * @param string $class_name Nombre completo de la clase
     * @return array Información completa de la clase
     */
    static function getClassInfo(string $class_name): array
    {
        if (isset(static::$class_cache[$class_name])) {
            return static::$class_cache[$class_name];
        }

        $reflection = new \ReflectionClass($class_name);
        
        return static::$class_cache[$class_name] = [
            'name' => $reflection->getName(),
            'namespace' => $reflection->getNamespaceName(),
            'doc_comment' => $reflection->getDocComment(),
            'is_abstract' => $reflection->isAbstract(),
            'constants' => static::getConstants($reflection),
            'properties' => static::getProperties($reflection),
            'methods' => static::getAllMethodsInfo($reflection),
            'constructor_info' => static::getConstructor($class_name)
        ];
    }

    /**
     * Obtiene las constantes definidas en la clase
     * 
     * @param \ReflectionClass $reflection
     * @return array
     */
    static function getConstants(\ReflectionClass $reflection): array
    {
        $constants = [];
        foreach ($reflection->getReflectionConstants() as $const) {
            $constants[] = [
                'name' => $const->getName(),
                'value' => $const->getValue(),
                'doc_comment' => $const->getDocComment()
            ];
        }
        return $constants;
    }

    /**
     * Obtiene información de todas las propiedades
     * 
     * @param \ReflectionClass $reflection
     * @return array
     */
    static function getProperties(\ReflectionClass $reflection): array
    {
        $properties = [];
        foreach ($reflection->getProperties() as $prop) {
            $properties[] = [
                'name' => $prop->getName(),
                'type' => $prop->getType() ? $prop->getType()->getName() : null,
                'doc_comment' => $prop->getDocComment(),
                'visibility' => (
                    $prop->isPrivate() ? 'private' : 
                    ($prop->isProtected() ? 'protected' : 'public')
                ),
                'is_static' => $prop->isStatic()
            ];
        }
        return $properties;
    }

    /**
     * Obtiene información detallada de todos los métodos
     * 
     * @param \ReflectionClass $reflection
     * @return array
     */
    static function getAllMethodsInfo(\ReflectionClass $reflection): array
    {
        $methods = [];
        foreach ($reflection->getMethods() as $method) {
            $methods[] = [
                'name' => $method->getName(),
                'doc_comment' => $method->getDocComment(),
                'visibility' => (
                    $method->isPrivate() ? 'private' : 
                    ($method->isProtected() ? 'protected' : 'public')
                ),
                'is_static' => $method->isStatic(),
                'parameters' => static::getMethodParameters($method),
                'return_type' => $method->getReturnType() ? 
                    $method->getReturnType()->getName() : null
            ];
        }
        return $methods;
    }

    /**
     * Obtiene información detallada de los parámetros de un método
     * 
     * @param \ReflectionMethod $method
     * @return array
     */
    static function getMethodParameters(\ReflectionMethod $method): array
    {
        $params = [];
        foreach ($method->getParameters() as $param) {
            $params[] = [
                'name' => $param->getName(),
                'type' => $param->getType() ? $param->getType()->getName() : null,
                'is_optional' => $param->isOptional(),
                'has_default' => $param->isDefaultValueAvailable(),
                'default_value' => $param->isDefaultValueAvailable() ? 
                    $param->getDefaultValue() : null,
                'is_variadic' => $param->isVariadic(),
                'is_passed_by_reference' => $param->isPassedByReference()
            ];
        }
        return $params;
    }

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
            return null;
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

    static function getMethods(string $class_name, $filter = null): array
    {
        $reflection = new \ReflectionClass($class_name);
        return $reflection->getMethods($filter);
    }

    /**
     * Retrieves public methods of a class
     * 
     * @param string $class_name The name of the class.
     * @return array An array containing the public methods
     */
    static function getPublicMethods(string $class_name): array
    {
        return static::getMethods($class_name, \ReflectionMethod::IS_PUBLIC);
    }

    /**
     * Retrieves public methods of a class that start with a specific prefix.
     * 
     * @param string $class_name The name of the class.
     * @param string $prefix The prefix to filter methods by.
     * @param int $filter For example \ReflectionMethod::IS_PUBLIC) to retrive public methods
     * @return array An array containing the names of public methods that start with the specified prefix.
     */
    static function getMethodsStartingWith(string $class_name, string $prefix, $filter = null): array
    {
        $methods   = static::getMethods($class_name);
        $callbacks = [];

        foreach ($methods as $method) {
            if (strpos($method->name, $prefix) === 0) {
                $callbacks[] = $method->name;
            }
        }

        return $callbacks;
    }

    /**
     * Genera DocBlocks para una clase o método si no los tiene
     * 
     * @param string $class_name
     * @param string|null $method_name
     * @return string
     */
    static function generateDocBlock(string $class_name, ?string $method_name = null): string
    {
        $reflection = new \ReflectionClass($class_name);
        
        if ($method_name !== null) {
            $method = $reflection->getMethod($method_name);
            $params = static::getMethodParameters($method);
            
            $doc = "/**\n";
            $doc .= " * [Descripción del método]\n";
            foreach ($params as $param) {
                $doc .= " * @param {$param['type']} \${$param['name']} [Descripción]\n";
            }
            if ($method->getReturnType()) {
                $doc .= " * @return {$method->getReturnType()->getName()} [Descripción]\n";
            }
            $doc .= " */";
            
            return $doc;
        }
        
        // DocBlock para la clase
        $doc = "/**\n";
        $doc .= " * [Descripción de la clase]\n";
        $doc .= " *\n";
        $doc .= " * @package " . $reflection->getNamespaceName() . "\n";
        $doc .= " */";
        
        return $doc;
    }

    /**
     * Analiza una clase y devuelve los métodos que carecen de DocBlock
     * 
     * @param string $class_name
     * @return array
     */
    static function findMethodsWithoutDocBlock(string $class_name): array
    {
        $reflection = new \ReflectionClass($class_name);
        $methods_without_doc = [];

        foreach ($reflection->getMethods() as $method) {
            if (!$method->getDocComment()) {
                $methods_without_doc[] = $method->getName();
            }
        }

        return $methods_without_doc;
    }
}
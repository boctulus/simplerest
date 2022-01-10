<?php

namespace simplerest\libs;

class Reflector
{    
    static Array $info_constructor = [];

    static function getConstructor(string $class_name)
    {
        if (isset(static::$info_constructor[$class_name])){
            return static::$info_constructor[$class_name];
        }

        $reflection = new \ReflectionClass($class_name);
        $params = $reflection->getConstructor()->getParameters();

        $param_names     = [];
        $qty_required    = 0;
        $qty_optional    = 0;
        $required_params = []; 
        $info = [];

        foreach ($params as $ix => $param) {
            $param_name  = $param->name;
            $param_type  = $param->getType();

            $class_name  = $param->getName();
            
            $is_optional = $param->isOptional();
            $is_nullable = $param->allowsNull();
            $is_def_val_available = $param->isDefaultValueAvailable();

            if ($is_def_val_available){
                $def_val = $param->getDefaultValue();
            }
            
            $is_array    = $param_type === 'array';
            $is_callable = $param_type === 'callable';

            $is_passed_by_ref = $param->isPassedByReference();

            // dd($param_name, 'param name');
            // dd($is_passed_by_ref, 'by ref');
            // dd($param_type, 'param type');
            // dd($class_name, 'class name');
            // dd($is_optional, 'is is_optional');
            // dd($is_nullable, 'is nullable');
            // dd($is_def_val_available, 'has def value');
            // dd($def_val ?? 'None', 'def value');
            // dd($is_array, 'is array');
            // dd($is_callable, 'is callable');
            // d('----------------------------');

            $param_names[] = $param_name;

            if (!$is_optional){
                $qty_required++;
                $required_params[] = $param_name;
            } else {
                $qty_optional++;
            }

            $info[] = [
                'param_name'    => $param_name,
                'required'      => !$is_optional,
                'is_array'      => $is_array
            ];
        }

        static::$info_constructor[$class_name] = [
            'param_names'    => $param_names,
            'optional_qty'   => $qty_optional,
            'required_qty'   => $qty_required,
            'required_parms' => $required_params,
            'params' => $info
        ];

        return static::$info_constructor[$class_name];
    }


}


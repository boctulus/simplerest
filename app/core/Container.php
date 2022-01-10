<?php

namespace simplerest\core;

/*
    Contenedor de dependencias

    @author Bozzolo Pablo <boctulus>
*/

use simplerest\libs\Reflector;

class Container 
{
    static protected $bindings = [];

    /*
        $key podría ser algo como 'foo' o  'Bloom\Security\ChannelAuthInterface'
    */
    static public function bind(string $key, $value, bool $singleton = false)
    {
        static::$bindings[$key] = [
            'value' => $value,
            'is_singleton' => $singleton
        ];
    }

    static public function singleton(string $key, $value){
        static::bind($key, $value, true);
    }

    static public function make(string $key, Array $params = [])
    {
        if (!isset(static::$bindings[$key])) {
            throw new \InvalidArgumentException("Class not found");          
        }

        if (!is_object(static::$bindings[$key]['value'])){
            if (class_exists(static::$bindings[$key]['value'])){
                $params_to_pass = [];

                $refl    = Reflector::getConstructor(static::$bindings[$key]['value']);
                $_params     = $refl['params'];
                $req_params  = $refl['required_parms'];
                $req_qty     = $refl['required_qty'];
                $param_names = $refl['param_names'];

                if ($req_qty != 0){
                    $cnt_params = count($params);
                    if ($cnt_params < $req_qty){
                        throw new \Exception("Expecting a minimum of $req_qty parameters but get only $cnt_params");
                    }

                    foreach ($req_params as $p_name){
                        if (!isset($params[$p_name])){
                            throw new \Exception("Parameter '$p_name' not found");
                        }
                    }

                    foreach ($param_names as $p_name){
                        if (isset($params[$p_name])){
                            $params_to_pass[] = $params[$p_name];
                        } else {
                            $params_to_pass[] = null;
                        }
                    }
                }
                

                /*
                    Instancio objeto y acá debería "bindear" parámetros de ser necesarios
                */
                $obj = new static::$bindings[$key]['value'](...$params_to_pass);

                if (static::$bindings[$key]['is_singleton']){
                    static::$bindings[$key]['value'] = $obj;
                }

                return $obj;
            } else {
                throw new \Exception("Invalid argumement");
            }
        } else {
            if (static::$bindings[$key]['value'] instanceof \Closure){
                return (static::$bindings[$key]['value'])->__invoke();
            }

            return (static::$bindings[$key]['value']);
        }
    }


    static public function makeWith(string $key, Array $params){
        return static::make($key, $params);
    }
    
}
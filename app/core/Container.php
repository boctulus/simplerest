<?php

namespace Boctulus\Simplerest\Core;

/*
    Contenedor de dependencias

    @author Bozzolo Pablo <boctulus>
*/

use Boctulus\Simplerest\Core\Libs\Reflector;
use Boctulus\Simplerest\Core\Libs\Config;

class Container 
{
    static protected $bindings = [];
    static protected $class_contracts = [];
    static protected $contracts = [];

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

    /*
        Ej:

        Container::singleton('foo', Foo::class);

        $foo = Container::make('foo');
        print_r($foo->bar());
        
        $foo = Container::make('foo');
        print_r($foo->bar());
    */
    static public function singleton(string $key, $value){
        static::bind($key, $value, true);
    }

    public static function getImplementation(string $interface): ?string {
        foreach (static::$contracts as $class => $interfaces) {
            if (isset($interfaces[$interface])) {
                return $interfaces[$interface];
            }
        }
        return null;
    }

    static public function make(string $key, Array $params = [])
    {
        // Si es una interfaz, buscar implementación
        if (interface_exists($key)) {
            $implementation = static::getImplementation($key);
            if ($implementation === null) {
                throw new \InvalidArgumentException("No implementation found for interface: $key");
            }
            $key = $implementation;
        }
            
        if (!isset(static::$bindings[$key])) {
            throw new \InvalidArgumentException("Class not found");          
        }
    
        if (!is_object(static::$bindings[$key]['value'])){
            if (class_exists(static::$bindings[$key]['value'])){
                $params_to_pass = [];

                $refl        = Reflector::getConstructor(static::$bindings[$key]['value']);   
                
                $_params     = $refl['params'];
                $req_params  = $refl['required_params'];
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

                    /*
                        En PHP 8.0 hacer esto no tendría sentido porque se pueden pasar
                        parámetros nombrados => el órden no importa.
                    */
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

    /*
        WHEN some class NEEDS an interface then GIVE some implementation instance instead

        ->when()
        ->needs()
        ->give()

        https://stackoverflow.com/questions/52777570/laravel-5-how-to-use-this-app-when
    */
    static public function useContract(string $class, string $interface, string $implementation_class){
        if (!isset(static::$contracts[$class])){
            static::$contracts[$class] = [];
        }

        static::$contracts[$class][$interface] = $implementation_class;
    }
    
}
<?php

namespace Boctulus\Simplerest\Core\Traits;

trait Uuids
{
    // protected function boot()
    // {
    //     parent::boot();

    //     // ...
    // }    

    protected function init()
    {
        parent::init();

        $this->registerInputMutator($this->getIdName(), function($id){
            // Usa la extensión ext-uuid si está disponible; si no, genera un UUID v4
            // inline. NO declarar una función con nombre dentro del método: init()
            // corre por cada instancia de modelo y redeclararla es fatal.
            if (function_exists('\uuid_create')) {
                return \uuid_create(4); // UUID_TYPE_RANDOM
            }

            $data = random_bytes(16);
            $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // version 4
            $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variant 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		}, function($op, $dato){
            return ($op == 'CREATE');
		});  
    }    
}

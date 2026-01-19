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
            if (!is_defined('UUID_TYPE_RANDOM')) {
                define('UUID_TYPE_RANDOM', 4);
            }

			return uuid_create(UUID_TYPE_RANDOM); 
		}, function($op, $dato){
            return ($op == 'CREATE');
		});  
    }    
}

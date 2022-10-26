<?php

namespace simplerest\models;

use simplerest\core\Model;

class MyModel extends Model 
{
    // protected $createdAt = 'gen_dtimFechaActualizacion';
    // protected $createdBy = 'usu_intIdCreador';
	// protected $updatedBy = 'usu_intIdActualizador';	
    
    function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);
    }

    function wp(){
		return $this->prefix('wp_');
	}

    protected function boot(){          
        if (empty($this->prefix)){
			$this->wp();
		}       
    }

    protected function init(){		
		
	}
}
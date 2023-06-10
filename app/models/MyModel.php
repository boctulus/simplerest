<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\core\libs\DB;

class MyModel extends Model 
{
    // protected $createdAt = 'gen_dtimFechaActualizacion';
    // protected $createdBy = 'usu_intIdCreador';
	// protected $updatedBy = 'usu_intIdActualizador';	
    
    function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);

        $this->hide([
            'created_at',
            'updated_at',
            'deleted_at'
        ]);

        $this->field_names = [
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At'
        ];
    }

    function wp(){
		return $this->prefix('wp_');
	}

    protected function boot(){          
        // if (empty($this->prefix) && (in_array(DB::getCurrentConnectionId(), ['woo3', null]))){
		// 	$this->wp();
		// }        
    }

    protected function init(){		
		
	}
}
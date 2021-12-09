<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\core\Model;
use simplerest\libs\ValidationRules;

/*
	La ruta depende del nombre de la conexión por defecto o sea será algo como:

	simplerest\schemas\{nombre_conexion_default}\UsersSchema
*/
use simplerest\schemas\az\UsersSchema;  

class UsersModel extends MyModel
 { 	
	protected $hidden   = [	'password' ];
	protected $not_fillable = ['confirmed_email', 'is_active'];

    function __construct(bool $connect = false){		
		$this->registerInputMutator('password', function($pass){ 
			return password_hash($pass, PASSWORD_DEFAULT); 
		}, function($op, $dato){
			return ($dato !== null);
		});

		//$this->registerOutputMutator('password', function($pass){ return '******'; } );
        parent::__construct($connect, UsersSchema::class);
	}
	
	// Hooks
	function onUpdating(&$data) {
		if ($this->isDirty('email')) {
			$this->fill(['confirmed_email'])->update(['confirmed_email' => 0]);
		}
	}
}


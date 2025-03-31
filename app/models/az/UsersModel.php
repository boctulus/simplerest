<?php

namespace Boctulus\Simplerest\Models\az;

use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\UsersSchema;

/*
	La ruta depende del nombre de la conexión por defecto o sea será algo como:

	Boctulus\Simplerest\Schemas\{nombre_conexion_default}\UsersSchema

	Ej:

	use Boctulus\Simplerest\Schemas\az\UsersSchema;  
*/

class UsersModel extends Model
 { 	
	protected $hidden       = [	'password' ];
	protected $not_fillable = ['confirmed_email', 'is_active'];

	public static $email           = 'email';
	public static $username        = 'username';
	public static $password        = 'password';
	public static $confirmed_email = 'confirmed_email';
	public static $active          = 'is_active';

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


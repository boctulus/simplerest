<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\main\UsersSchema;

class UsersModel extends Model
 { 	
	protected $hidden   = [	'password' ];
	protected $not_fillable = ['confirmed_email', 'active'];
	
	public    $active    = 'active';
	public 	  $username  = 'username';
	public 	  $email 	 = 'email';

    function __construct(bool $connect = false){		
		$this->registerInputMutator('password', function($pass){ 
			return password_hash($pass, PASSWORD_DEFAULT); 
		}, function($op, $dato){
			return ($dato !== null);
		});

		//$this->registerOutputMutator('password', function($pass){ return '******'; } );
        parent::__construct($connect, new UsersSchema());
	}
	
	// Hooks
	function onUpdating(&$data) {
		if ($this->isDirty('email')) {
			$this->fill(['confirmed_email'])->update(['confirmed_email' => 0]);
		}
	}
}


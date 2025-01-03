<?php

namespace simplerest\models\main;


use simplerest\models\MyModel;
use simplerest\schemas\main\UsersSchema;

class UsersModel extends MyModel
{
	protected $hidden   = [	'password' ];
	protected $not_fillable = ['confirmed_email', 'is_active'];

	static public $email    = 'email';
	static public $username = 'username';
	static public $password = 'password';
	static public $confirmed_email = 'confirmed_email';
	static public $is_active = 'is_active';

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


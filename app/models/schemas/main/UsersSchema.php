<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'users',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'username' => 'STR',
				'active' => 'INT',
				'locked' => 'INT',
				'email' => 'STR',
				'confirmed_email' => 'INT',
				'firstname' => 'STR',
				'lastname' => 'STR',
				'password' => 'STR',
				'deleted_at' => 'STR'
			],

			'nullable'		=> ['id', 'active', 'confirmed_email', 'firstname', 'lastname', 'password', 'deleted_at'],

			'rules' 		=> [
				'username' => ['max' => 15],
				'email' => ['max' => 60],
				'firstname' => ['max' => 50],
				'lastname' => ['max' => 80],
				'password' => ['max' => 60]
			],

			'relationships' => [
				'user_tb_permissions' => [
					['user_tb_permissions.user_id','users.use_intId']
				],
				'api_keys' => [
					['api_keys.user_id','users.use_intId']
				],
				'user_roles' => [
					['user_roles.user_id','users.use_intId']
				],
				'user_sp_permissions' => [
					['user_sp_permissions.user_id','users.use_intId']
				]
			]
		];
	}	
}


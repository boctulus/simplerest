<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UserRolesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'user_roles',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'user_id' => 'INT',
				'role_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'user_id' => ['type' => 'int', 'required' => true],
				'role_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
			],

			'relationships' => [
				'users' => [
					['users.id','user_roles.user_id']
				]
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UserRolesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'user_roles',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'STR',
				'user_id' => 'INT',
				'role_id' => 'STR',
				'created_by' => 'INT',
				'created_at' => 'STR',
				'updated_by' => 'INT',
				'updated_at' => 'STR'
			],

			'nullable'		=> ['id', 'created_by', 'updated_by', 'updated_at'],

			'rules' 		=> [

			],

			'relationships' => [
				'roles' => [
					['roles.id','user_roles.role_id']
				]
			]
		];
	}	
}


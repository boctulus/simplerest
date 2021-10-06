<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UserSpPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'user_sp_permissions',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'sp_permission_id' => 'INT',
				'user_id' => 'INT',
				'created_by' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'nullable'		=> ['id', 'created_by', 'updated_at'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'sp_permission_id' => ['type' => 'int', 'required' => true],
				'user_id' => ['type' => 'int', 'required' => true],
				'created_by' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'relationships' => [
				'sp_permissions' => [
					['sp_permissions.id','user_sp_permissions.sp_permission_id']
				],
				'users' => [
					['users.id','user_sp_permissions.user_id']
				]
			]
		];
	}	
}


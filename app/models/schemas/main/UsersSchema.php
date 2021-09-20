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
				'firstname' => 'STR',
				'lastname' => 'STR',
				'username' => 'STR',
				'password' => 'STR',
				'active' => 'INT',
				'locked' => 'INT',
				'email' => 'STR',
				'confirmed_email' => 'INT',
				'belongs_to' => 'INT',
				'created_by' => 'INT',
				'updated_by' => 'INT',
				'deleted_by' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'nullable'		=> ['id', 'firstname', 'lastname', 'password', 'active', 'confirmed_email', 'belongs_to', 'created_by', 'updated_by', 'deleted_by', 'updated_at', 'deleted_at'],

			'rules' 		=> [
				'firstname' => ['max' => 50],
				'lastname' => ['max' => 80],
				'username' => ['max' => 15],
				'password' => ['max' => 60],
				'email' => ['max' => 60]
			],

			'relationships' => [
				'users' => [
					['updated_bys.id','users.updated_by'],
					['updated_byss.id','users.deleted_by'],
					['updated_bysss.id','users.belongs_to'],
					['updated_byssss.id','users.created_by'],
					['users.deleted_by','users.id'],
					['users.belongs_to','users.id'],
					['users.created_by','users.id'],
					['users.updated_by','users.id']
				],
				'user_roles' => [
					['user_roles.user_id','users.use_intId']
				],
				'user_sp_permissions' => [
					['user_sp_permissions.user_id','users.use_intId']
				],
				'api_keys' => [
					['api_keys.user_id','users.use_intId']
				],
				'user_tb_permissions' => [
					['user_tb_permissions.user_id','users.use_intId']
				]
			]
		];
	}	
}


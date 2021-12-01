<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UserTbPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'user_tb_permissions',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'tb' => 'STR',
				'can_list_all' => 'INT',
				'can_show_all' => 'INT',
				'can_list' => 'INT',
				'can_show' => 'INT',
				'can_create' => 'INT',
				'can_update' => 'INT',
				'can_delete' => 'INT',
				'user_id' => 'INT',
				'created_by' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'can_list_all', 'can_show_all', 'can_list', 'can_show', 'can_create', 'can_update', 'can_delete', 'created_by', 'updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'tb' => ['type' => 'str', 'max' => 80, 'required' => true],
				'can_list_all' => ['type' => 'bool'],
				'can_show_all' => ['type' => 'bool'],
				'can_list' => ['type' => 'bool'],
				'can_show' => ['type' => 'bool'],
				'can_create' => ['type' => 'bool'],
				'can_update' => ['type' => 'bool'],
				'can_delete' => ['type' => 'bool'],
				'user_id' => ['type' => 'int', 'required' => true],
				'created_by' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['user_id'],

			'relationships' => [
				'users' => [
					['users.id','user_tb_permissions.user_id']
				]
			],

			'expanded_relationships' => array (
				  'users' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'users',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'user_tb_permissions',
				        1 => 'user_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'users' => [
					['users.id','user_tb_permissions.user_id']
				]
			],

			'expanded_relationships_from' => array (
				  'users' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'users',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'user_tb_permissions',
				        1 => 'user_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}


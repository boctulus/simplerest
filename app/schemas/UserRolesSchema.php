<?php

namespace simplerest\schemas;

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

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'user_id' => ['type' => 'int', 'required' => true],
				'role_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['role_id', 'user_id'],

			'relationships' => [
				'roles' => [
					['roles.id','user_roles.role_id']
				],
				'users' => [
					['users.id','user_roles.user_id']
				]
			],

			'expanded_relationships' => array (
				  'roles' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'roles',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'user_roles',
				        1 => 'role_id',
				      ),
				    ),
				  ),
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
				        0 => 'user_roles',
				        1 => 'user_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'roles' => [
					['roles.id','user_roles.role_id']
				],
				'users' => [
					['users.id','user_roles.user_id']
				]
			],

			'expanded_relationships_from' => array (
				  'roles' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'roles',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'user_roles',
				        1 => 'role_id',
				      ),
				    ),
				  ),
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
				        0 => 'user_roles',
				        1 => 'user_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}

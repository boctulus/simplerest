<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UserRolesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'user_roles',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'user_id', 'role_id', 'created_by', 'created_at', 'updated_by', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'user_id' => 'INT',
				'role_id' => 'INT',
				'created_by' => 'INT',
				'created_at' => 'STR',
				'updated_by' => 'INT',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'created_by', 'updated_by', 'updated_at'],

			'required'			=> ['user_id', 'role_id', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'user_id' => ['type' => 'int', 'required' => true],
				'role_id' => ['type' => 'int', 'required' => true],
				'created_by' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_by' => ['type' => 'int'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 				=> ['user_id', 'role_id'],

			'relationships' => [
				'users' => [
					['users.id','user_roles.user_id']
				],
				'roles' => [
					['roles.id','user_roles.role_id']
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
        0 => 'user_roles',
        1 => 'user_id',
      ),
    ),
  ),
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
),

			'relationships_from' => [
				'users' => [
					['users.id','user_roles.user_id']
				],
				'roles' => [
					['roles.id','user_roles.role_id']
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
        0 => 'user_roles',
        1 => 'user_id',
      ),
    ),
  ),
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
)
		];
	}	
}


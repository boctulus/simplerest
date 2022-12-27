<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UserSpPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'user_sp_permissions',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'sp_permission_id', 'user_id', 'created_by', 'created_at', 'updated_by', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'sp_permission_id' => 'INT',
				'user_id' => 'INT',
				'created_by' => 'INT',
				'created_at' => 'STR',
				'updated_by' => 'INT',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_by', 'created_at', 'updated_by', 'updated_at'],

			'required'		=> ['sp_permission_id', 'user_id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'sp_permission_id' => ['type' => 'int', 'required' => true],
				'user_id' => ['type' => 'int', 'required' => true],
				'created_by' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime'],
				'updated_by' => ['type' => 'int'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['sp_permission_id', 'user_id'],

			'relationships' => [
				'sp_permissions' => [
					['sp_permissions.id','user_sp_permissions.sp_permission_id']
				],
				'users' => [
					['users.id','user_sp_permissions.user_id']
				]
			],

			'expanded_relationships' => array (
  'sp_permissions' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'sp_permissions',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'user_sp_permissions',
        1 => 'sp_permission_id',
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
        0 => 'user_sp_permissions',
        1 => 'user_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'sp_permissions' => [
					['sp_permissions.id','user_sp_permissions.sp_permission_id']
				],
				'users' => [
					['users.id','user_sp_permissions.user_id']
				]
			],

			'expanded_relationships_from' => array (
  'sp_permissions' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'sp_permissions',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'user_sp_permissions',
        1 => 'sp_permission_id',
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
        0 => 'user_sp_permissions',
        1 => 'user_id',
      ),
    ),
  ),
)
		];
	}	
}


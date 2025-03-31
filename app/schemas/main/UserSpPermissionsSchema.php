<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class UserSpPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'user_sp_permissions',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'sp_permission_id', 'user_id', 'created_by', 'created_at', 'updated_by', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'sp_permission_id' => 'INT',
				'user_id' => 'INT',
				'created_by' => 'INT',
				'created_at' => 'STR',
				'updated_by' => 'INT',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'created_by', 'created_at', 'updated_by', 'updated_at'],

			'required'			=> ['sp_permission_id', 'user_id'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'sp_permission_id' => ['type' => 'int', 'required' => true],
				'user_id' => ['type' => 'int', 'required' => true],
				'created_by' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime'],
				'updated_by' => ['type' => 'int'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 				=> ['user_id', 'sp_permission_id'],

			'relationships' => [
				'users' => [
					['users.id','user_sp_permissions.user_id']
				],
				'sp_permissions' => [
					['sp_permissions.id','user_sp_permissions.sp_permission_id']
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
        0 => 'user_sp_permissions',
        1 => 'user_id',
      ),
    ),
  ),
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
),

			'relationships_from' => [
				'users' => [
					['users.id','user_sp_permissions.user_id']
				],
				'sp_permissions' => [
					['sp_permissions.id','user_sp_permissions.sp_permission_id']
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
        0 => 'user_sp_permissions',
        1 => 'user_id',
      ),
    ),
  ),
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
)
		];
	}	
}


<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class SpPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'sp_permissions',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['name'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 45, 'required' => true]
			],

			'fks' 				=> [],

			'relationships' => [
				'user_sp_permissions' => [
					['user_sp_permissions.sp_permission_id','sp_permissions.id']
				]
			],

			'expanded_relationships' => array (
  'user_sp_permissions' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'user_sp_permissions',
        1 => 'sp_permission_id',
      ),
      1 => 
      array (
        0 => 'sp_permissions',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}


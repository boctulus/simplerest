<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class RolesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'roles',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'name'],

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'required'		=> ['name'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 50, 'required' => true]
			],

			'fks' 			=> [],

			'relationships' => [
				'user_roles' => [
					['user_roles.role_id','roles.id']
				]
			],

			'expanded_relationships' => array (
  'user_roles' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'user_roles',
        1 => 'role_id',
      ),
      1 => 
      array (
        0 => 'roles',
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


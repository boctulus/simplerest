<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ApiKeysSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'api_keys',

			'id_name'		=> 'uuid',

			'fields'		=> ['uuid', 'value', 'user_id', 'created_at'],

			'attr_types'	=> [
				'uuid' => 'STR',
				'value' => 'STR',
				'user_id' => 'INT',
				'created_at' => 'STR'
			],

			'primary'		=> ['uuid'],

			'autoincrement' => null,

			'nullable'		=> ['created_at'],

			'required'		=> ['uuid', 'value', 'user_id'],

			'uniques'		=> [],

			'rules' 		=> [
				'uuid' => ['type' => 'str', 'max' => 36, 'required' => true],
				'value' => ['type' => 'str', 'max' => 60, 'required' => true],
				'user_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['user_id'],

			'relationships' => [
				'users' => [
					['users.id','api_keys.user_id']
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
        0 => 'api_keys',
        1 => 'user_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users.id','api_keys.user_id']
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
        0 => 'api_keys',
        1 => 'user_id',
      ),
    ),
  ),
)
		];
	}	
}


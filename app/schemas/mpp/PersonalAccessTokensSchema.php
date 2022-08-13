<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class PersonalAccessTokensSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'personal_access_tokens',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'tokenable_type', 'tokenable_id', 'name', 'token', 'abilities', 'last_used_at', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'tokenable_type' => 'STR',
				'tokenable_id' => 'INT',
				'name' => 'STR',
				'token' => 'STR',
				'abilities' => 'STR',
				'last_used_at' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'abilities', 'last_used_at', 'created_at', 'updated_at'],

			'required'		=> ['tokenable_type', 'tokenable_id', 'name', 'token'],

			'uniques'		=> ['token'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'tokenable_type' => ['type' => 'str', 'max' => 255, 'required' => true],
				'tokenable_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'name' => ['type' => 'str', 'max' => 255, 'required' => true],
				'token' => ['type' => 'str', 'max' => 64, 'required' => true],
				'abilities' => ['type' => 'str'],
				'last_used_at' => ['type' => 'timestamp'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}


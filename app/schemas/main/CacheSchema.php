<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CacheSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'cache',

			'id_name'			=> 'id',

			'fields'			=> ['id', '_key_', 'value', 'expires_at', 'cached_at', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'_key_' => 'STR',
				'value' => 'STR',
				'expires_at' => 'INT',
				'cached_at' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'cached_at', 'updated_at'],

			'required'			=> ['_key_', 'value', 'expires_at', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'_key_' => ['type' => 'str', 'max' => 191, 'required' => true],
				'value' => ['type' => 'str', 'required' => true],
				'expires_at' => ['type' => 'int', 'required' => true],
				'cached_at' => ['type' => 'timestamp'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 				=> [],

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


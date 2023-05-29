<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CacheSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'cache',

			'id_name'			=> 'the_key',

			'fields'			=> ['the_key', 'the_value', 'cached_at', 'expiration_time'],

			'attr_types'		=> [
				'the_key' => 'STR',
				'the_value' => 'STR',
				'cached_at' => 'INT',
				'expiration_time' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['the_key'],

			'autoincrement' 	=> null,

			'nullable'			=> ['cached_at'],

			'required'			=> ['the_key', 'the_value', 'expiration_time'],

			'uniques'			=> [],

			'rules' 			=> [
				'the_key' => ['type' => 'str', 'max' => 60, 'required' => true],
				'the_value' => ['type' => 'str', 'required' => true],
				'cached_at' => ['type' => 'int'],
				'expiration_time' => ['type' => 'int', 'required' => true]
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


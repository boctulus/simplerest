<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class OptionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'options',

			'id_name'			=> 'the_key',

			'fields'			=> ['the_key', 'the_val', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'the_key' => 'STR',
				'the_val' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['the_key'],

			'autoincrement' 	=> null,

			'nullable'			=> [],

			'required'			=> ['the_key', 'the_val', 'created_at', 'updated_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'the_key' => ['type' => 'str', 'max' => 60, 'required' => true],
				'the_val' => ['type' => 'str', 'max' => 240, 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime', 'required' => true]
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


<?php

namespace simplerest\schemas\parts;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class PartNumbersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'part_numbers',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'nombre', 'nota', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'nombre' => 'STR',
				'nota' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'nombre', 'nota', 'created_at', 'updated_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 255],
				'nota' => ['type' => 'str', 'max' => 255],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
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


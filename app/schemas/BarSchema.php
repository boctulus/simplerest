<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class BarSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'bar',

			'id_name'		=> 'uuid',

			'attr_types'	=> [
				'uuid' => 'STR',
				'name' => 'STR',
				'price' => 'STR',
				'email' => 'STR',
				'belongs_to' => 'INT',
				'created_at' => 'STR',
				'deleted_at' => 'STR',
				'updated_at' => 'STR',
				'ts' => 'STR'
			],

			'primary'		=> ['uuid'],

			'autoincrement' => null,

			'nullable'		=> ['created_at', 'deleted_at', 'updated_at', 'ts'],

			'uniques'		=> [],

			'rules' 		=> [
				'uuid' => ['type' => 'str', 'max' => 36, 'required' => true],
				'name' => ['type' => 'str', 'max' => 50, 'required' => true],
				'price' => ['type' => 'decimal(15,2)', 'required' => true],
				'email' => ['type' => 'str', 'max' => 80, 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime'],
				'deleted_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime'],
				'ts' => ['type' => 'str']
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


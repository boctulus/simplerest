<?php

namespace Boctulus\Simplerest\Schemas\az;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class Baz2Schema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'baz2',

			'id_name'		=> 'id_baz2',

			'fields'		=> ['id_baz2', 'name', 'cost'],

			'attr_types'	=> [
				'id_baz2' => 'INT',
				'name' => 'STR',
				'cost' => 'STR'
			],

			'primary'		=> ['id_baz2'],

			'autoincrement' => 'id_baz2',

			'nullable'		=> ['id_baz2'],

			'required'		=> ['name', 'cost'],

			'uniques'		=> [],

			'rules' 		=> [
				'id_baz2' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 45, 'required' => true],
				'cost' => ['type' => 'decimal(5,2)', 'required' => true]
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


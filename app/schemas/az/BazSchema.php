<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class BazSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'baz',

			'id_name'		=> 'id_baz',

			'fields'		=> ['id_baz', 'name', 'cost'],

			'attr_types'	=> [
				'id_baz' => 'INT',
				'name' => 'STR',
				'cost' => 'STR'
			],

			'primary'		=> ['id_baz'],

			'autoincrement' => null,

			'nullable'		=> [],

			'required'		=> ['id_baz', 'name', 'cost'],

			'uniques'		=> [],

			'rules' 		=> [
				'id_baz' => ['type' => 'int', 'required' => true],
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


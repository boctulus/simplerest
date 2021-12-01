<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TestxSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'testx',

			'id_name'		=> null,

			'attr_types'	=> [
				'deci' => 'STR'
			],

			'primary'		=> [],

			'autoincrement' => null,

			'nullable'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'deci' => ['type' => 'decimal(5,2)', 'required' => true]
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


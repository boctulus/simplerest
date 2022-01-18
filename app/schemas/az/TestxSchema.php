<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TestxSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'testx',

			'id_name'		=> null,

			'attr_types'	=> [
				'deci' => 'STR',
				'fecha' => 'STR'
			],

			'primary'		=> [],

			'autoincrement' => null,

			'nullable'		=> ['fecha'],

			'uniques'		=> [],

			'rules' 		=> [
				'deci' => ['type' => 'decimal(5,2)', 'required' => true],
				'fecha' => ['type' => 'date']
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


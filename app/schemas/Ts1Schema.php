<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class Ts1Schema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'ts1',

			'id_name'		=> null,

			'attr_types'	=> [
				'ts1' => 'STR'
			],

			'primary'		=> [],

			'autoincrement' => null,

			'nullable'		=> ['ts1'],

			'uniques'		=> [],

			'rules' 		=> [
				'ts1' => ['type' => 'timestamp']
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


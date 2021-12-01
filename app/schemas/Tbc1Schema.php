<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class Tbc1Schema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbc1',

			'id_name'		=> 'p3',

			'attr_types'	=> [
				'p1' => 'INT',
				'p2' => 'INT',
				'p3' => 'STR',
				'name' => 'STR'
			],

			'primary'		=> ['p1', 'p2', 'p3'],

			'autoincrement' => null,

			'nullable'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'p1' => ['type' => 'int', 'required' => true],
				'p2' => ['type' => 'int', 'required' => true],
				'p3' => ['type' => 'str', 'max' => 50, 'required' => true],
				'name' => ['type' => 'str', 'max' => 60, 'required' => true]
			],

			'fks' 			=> [],

			'relationships' => [
				'tbc2' => [
					['tbc2.q3','tbc1.p3']
				]
			],

			'expanded_relationships' => array (
				  'tbc2' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbc2',
				        1 => 'q3',
				      ),
				      1 => 
				      array (
				        0 => 'tbc1',
				        1 => 'p3',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}


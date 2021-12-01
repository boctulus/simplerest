<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class Tbc2Schema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbc2',

			'id_name'		=> 'q1',

			'attr_types'	=> [
				'q1' => 'INT',
				'q3' => 'STR',
				'description' => 'STR'
			],

			'primary'		=> ['q1', 'q3'],

			'autoincrement' => 'q1',

			'nullable'		=> ['q1'],

			'uniques'		=> [],

			'rules' 		=> [
				'q1' => ['type' => 'int'],
				'q3' => ['type' => 'str', 'max' => 50, 'required' => true],
				'description' => ['type' => 'str', 'max' => 100, 'required' => true]
			],

			'fks' 			=> ['q3'],

			'relationships' => [
				'tbc1' => [
					['tbc1.p3','tbc2.q3']
				]
			],

			'expanded_relationships' => array (
				  'tbc1' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbc1',
				        1 => 'p3',
				      ),
				      1 => 
				      array (
				        0 => 'tbc2',
				        1 => 'q3',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbc1' => [
					['tbc1.p3','tbc2.q3']
				]
			],

			'expanded_relationships_from' => array (
				  'tbc1' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbc1',
				        1 => 'p3',
				      ),
				      1 => 
				      array (
				        0 => 'tbc2',
				        1 => 'q3',
				      ),
				    ),
				  ),
				)
		];
	}	
}


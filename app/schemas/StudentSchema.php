<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class StudentSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'student',

			'id_name'		=> null,

			'attr_types'	=> [
				'id' => 'STR',
				'name' => 'STR',
				'age' => 'INT',
				'class' => 'STR'
			],

			'primary'		=> [],

			'autoincrement' => null,

			'nullable'		=> ['id', 'name', 'age', 'class'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'str'],
				'name' => ['type' => 'str'],
				'age' => ['type' => 'int'],
				'class' => ['type' => 'str']
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


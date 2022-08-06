<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TeachersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'teachers',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => null,

			'nullable'		=> ['name'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'required' => true],
				'name' => ['type' => 'str']
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


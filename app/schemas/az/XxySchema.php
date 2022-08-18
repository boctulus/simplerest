<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class XxySchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'xxy',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'otro', 'otro2'],

			'attr_types'	=> [
				'id' => 'INT',
				'otro' => 'STR',
				'otro2' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'required'		=> ['otro', 'otro2'],

			'uniques'		=> ['otro2'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'otro' => ['type' => 'str', 'required' => true],
				'otro2' => ['type' => 'int', 'required' => true]
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


<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class JobsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'jobs',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'object' => 'STR',
				'params' => 'STR',
				'created_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'object' => ['type' => 'str', 'required' => true],
				'params' => ['type' => 'str', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
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


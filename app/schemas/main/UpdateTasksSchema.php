<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UpdateTasksSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'update_tasks',

			'id_name'		=> null,

			'attr_types'	=> [
				'uuid' => 'STR',
				'filename' => 'STR',
				'created_at' => 'STR'
			],

			'primary'		=> [],

			'autoincrement' => null,

			'nullable'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'uuid' => ['type' => 'str', 'max' => 36, 'required' => true],
				'filename' => ['type' => 'str', 'max' => 50, 'required' => true],
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


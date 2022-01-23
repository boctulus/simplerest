<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class BackgroundProcessSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'background_process',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'job_file' => 'STR',
				'pid' => 'INT',
				'created_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> ['pid'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'job_file' => ['type' => 'str', 'max' => 60, 'required' => true],
				'pid' => ['type' => 'int', 'required' => true],
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


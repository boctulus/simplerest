<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class BackgroundProcessSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'background_process',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'job', 'pid', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'job' => 'STR',
				'pid' => 'INT',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['job', 'pid', 'created_at'],

			'uniques'			=> ['pid'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'job' => ['type' => 'str', 'max' => 60, 'required' => true],
				'pid' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
			],

			'fks' 				=> [],

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


<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class JobsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'jobs',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'queue', 'object', 'params', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'queue' => 'STR',
				'object' => 'STR',
				'params' => 'STR',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['queue', 'object', 'params', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'queue' => ['type' => 'str', 'max' => 60, 'required' => true],
				'object' => ['type' => 'str', 'required' => true],
				'params' => ['type' => 'str', 'required' => true],
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


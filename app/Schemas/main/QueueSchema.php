<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class QueueSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'queue',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'category', 'data', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'category' => 'STR',
				'data' => 'STR',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [
				'data' => 'JSON'
			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'category'],

			'required'			=> ['data', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'category' => ['type' => 'str', 'max' => 25],
				'data' => ['type' => 'str', 'required' => true],
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


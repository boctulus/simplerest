<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class PerfTestSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'perf_test',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'email', 'age', 'status', 'salary', 'notes', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR',
				'email' => 'STR',
				'age' => 'INT',
				'status' => 'STR',
				'salary' => 'STR',
				'notes' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'name', 'email', 'age', 'status', 'salary', 'notes', 'created_at', 'updated_at'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 100],
				'email' => ['type' => 'str', 'max' => 150],
				'age' => ['type' => 'int'],
				'status' => ['type' => 'str', 'max' => 20],
				'salary' => ['type' => 'decimal(12,2)'],
				'notes' => ['type' => 'str'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
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


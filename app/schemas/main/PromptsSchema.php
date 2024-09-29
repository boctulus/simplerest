<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class PromptsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'prompts',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'title', 'project', 'description', 'base_path', 'files', 'notes', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'title' => 'STR',
				'project' => 'INT',
				'description' => 'STR',
				'base_path' => 'STR',
				'files' => 'STR',
				'notes' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [
				'files' => 'JSON'
			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'title', 'project', 'base_path', 'notes', 'updated_at'],

			'required'			=> ['description', 'files', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'title' => ['type' => 'str', 'max' => 100],
				'project' => ['type' => 'int'],
				'description' => ['type' => 'str', 'required' => true],
				'base_path' => ['type' => 'str', 'max' => 100],
				'files' => ['type' => 'str', 'required' => true],
				'notes' => ['type' => 'str'],
				'created_at' => ['type' => 'datetime', 'required' => true],
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


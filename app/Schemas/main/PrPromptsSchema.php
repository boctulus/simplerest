<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class PrPromptsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'pr_prompts',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'project_type', 'body', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR',
				'project_type' => 'STR',
				'body' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['name', 'project_type', 'body', 'created_at', 'updated_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 60, 'required' => true],
				'project_type' => ['type' => 'str', 'required' => true],
				'body' => ['type' => 'str', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime', 'required' => true]
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


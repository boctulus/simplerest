<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class JobsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'jobs',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'class', 'queue', 'object', 'params', 'taken', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'class' => 'STR',
				'queue' => 'STR',
				'object' => 'STR',
				'params' => 'STR',
				'taken' => 'INT',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'taken'],

			'required'			=> ['class', 'queue', 'object', 'params', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'class' => ['type' => 'str', 'max' => 60, 'required' => true],
				'queue' => ['type' => 'str', 'max' => 60, 'required' => true],
				'object' => ['type' => 'str', 'required' => true],
				'params' => ['type' => 'str', 'required' => true],
				'taken' => ['type' => 'bool'],
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


<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class JobsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'jobs',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'queue' => 'STR',
				'payload' => 'STR',
				'attempts' => 'INT',
				'reserved_at' => 'INT',
				'available_at' => 'INT',
				'created_at' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'reserved_at'],

			'required'			=> ['queue', 'payload', 'attempts', 'available_at', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'queue' => ['type' => 'str', 'max' => 255, 'required' => true],
				'payload' => ['type' => 'str', 'required' => true],
				'attempts' => ['type' => 'bool', 'min' => 0, 'required' => true],
				'reserved_at' => ['type' => 'int', 'min' => 0],
				'available_at' => ['type' => 'int', 'min' => 0, 'required' => true],
				'created_at' => ['type' => 'int', 'min' => 0, 'required' => true]
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


<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class MigrationsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'migrations',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'db', 'filename', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'db' => 'STR',
				'filename' => 'STR',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'db', 'created_at'],

			'required'			=> ['filename'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'db' => ['type' => 'str', 'max' => 50],
				'filename' => ['type' => 'str', 'max' => 255, 'required' => true],
				'created_at' => ['type' => 'datetime']
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


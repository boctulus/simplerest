<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class BackgroundProcessSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'background_process',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'filename', 'pid', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'filename' => 'STR',
				'pid' => 'INT',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['filename', 'pid', 'created_at'],

			'uniques'			=> ['filename', 'pid'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'filename' => ['type' => 'str', 'max' => 60, 'required' => true],
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


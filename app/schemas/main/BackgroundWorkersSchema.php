<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class BackgroundWorkersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'background_workers',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'queue', 'pid', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'queue' => 'STR',
				'pid' => 'INT',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['queue', 'pid', 'created_at'],

			'uniques'			=> ['pid'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'queue' => ['type' => 'str', 'max' => 60, 'required' => true],
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


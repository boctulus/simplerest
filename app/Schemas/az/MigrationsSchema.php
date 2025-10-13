<?php

namespace Boctulus\Simplerest\Schemas\az;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class MigrationsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'migrations',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'migration', 'batch'],

			'attr_types'	=> [
				'id' => 'INT',
				'migration' => 'STR',
				'batch' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'required'		=> ['migration', 'batch'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'migration' => ['type' => 'str', 'max' => 255, 'required' => true],
				'batch' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> [],

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


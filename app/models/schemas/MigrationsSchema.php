<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class MigrationsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'migrations',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'migration' => 'STR',
				'batch' => 'INT'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'migration' => ['type' => 'str', 'max' => 255, 'required' => true],
				'batch' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				
			]
		];
	}	
}


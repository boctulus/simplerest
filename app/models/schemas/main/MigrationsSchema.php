<?php

namespace simplerest\models\schemas\main;

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
				'migration' => ['max' => 255]
			],

			'relationships' => [
				
			]
		];
	}	
}


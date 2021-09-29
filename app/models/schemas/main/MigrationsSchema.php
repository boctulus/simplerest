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
				'db' => 'STR',
				'filename' => 'STR',
				'created_at' => 'STR'
			],

			'nullable'		=> ['id', 'db', 'created_at'],

			'rules' 		=> [
				'db' => ['max' => 50],
				'filename' => ['max' => 255]
			],

			'relationships' => [
				
			]
		];
	}	
}


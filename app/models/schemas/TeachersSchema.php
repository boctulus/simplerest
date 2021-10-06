<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TeachersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'teachers',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR'
			],

			'nullable'		=> ['name'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'required' => true],
				'name' => ['type' => 'str']
			],

			'relationships' => [
				
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CablesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'cables',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'nombre' => 'STR',
				'calibre' => 'INT',
				'autogenerado' => 'STR'
			],

			'nullable'		=> ['id', 'autogenerado'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'nombre' => ['type' => 'str', 'max' => 40, 'required' => true],
				'calibre' => ['type' => 'int', 'required' => true],
				'autogenerado' => ['type' => 'str', 'max' => 255]
			],

			'relationships' => [
				
			]
		];
	}	
}


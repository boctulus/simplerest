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
				'id' => ['min' => 0],
				'nombre' => ['max' => 40],
				'autogenerado' => ['max' => 255]
			],

			'relationships' => [
				
			]
		];
	}	
}


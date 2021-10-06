<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TestxSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'testx',

			'id_name'		=> NULL,

			'attr_types'	=> [
				'deci' => 'STR'
			],

			'nullable'		=> [],

			'rules' 		=> [
				'deci' => ['type' => 'decimal(5,2)', 'required' => true]
			],

			'relationships' => [
				
			]
		];
	}	
}


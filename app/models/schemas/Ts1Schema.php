<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class Ts1Schema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'ts1',

			'id_name'		=> NULL,

			'attr_types'	=> [
				'ts1' => 'STR'
			],

			'nullable'		=> ['ts1'],

			'rules' 		=> [
				'ts1' => ['type' => 'str']
			],

			'relationships' => [
				
			]
		];
	}	
}


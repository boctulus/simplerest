<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class BazSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'baz',

			'id_name'		=> 'id_baz',

			'attr_types'	=> [
				'id_baz' => 'INT',
				'name' => 'STR',
				'cost' => 'STR'
			],

			'nullable'		=> [],

			'rules' 		=> [
				'id_baz' => ['type' => 'int', 'required' => true],
				'name' => ['type' => 'str', 'max' => 45, 'required' => true],
				'cost' => ['type' => 'decimal(5,2)', 'required' => true]
			],

			'relationships' => [
				
			]
		];
	}	
}


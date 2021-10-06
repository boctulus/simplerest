<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class Facturas2Schema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'facturas2',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'co' => 'STR',
				'edad' => 'INT',
				'firstname' => 'STR',
				'lastname' => 'STR',
				'username' => 'STR'
			],

			'nullable'		=> ['id', 'lastname'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'co' => ['type' => 'str', 'max' => 30, 'required' => true],
				'edad' => ['type' => 'int', 'min' => 0, 'required' => true],
				'firstname' => ['type' => 'str', 'max' => 60, 'required' => true],
				'lastname' => ['type' => 'str', 'max' => 60],
				'username' => ['type' => 'str', 'max' => 60, 'required' => true]
			],

			'relationships' => [
				
			]
		];
	}	
}


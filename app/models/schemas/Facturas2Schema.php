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
				'co' => ['max' => 30],
				'firstname' => ['max' => 60],
				'lastname' => ['max' => 60],
				'username' => ['max' => 60]
			],

			'relationships' => [
				
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class StudentsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'students',

			'id_name'		=> 'teacher_id',

			'attr_types'	=> [
				'name' => 'STR',
				'teacher_id' => 'INT',
				'phone' => 'STR'
			],

			'nullable'		=> ['phone'],

			'rules' 		=> [
				'name' => ['type' => 'str', 'max' => 50, 'required' => true],
				'teacher_id' => ['type' => 'int', 'required' => true],
				'phone' => ['type' => 'str', 'max' => 20]
			],

			'relationships' => [
				
			]
		];
	}	
}


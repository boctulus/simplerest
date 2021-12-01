<?php

namespace simplerest\schemas\az;

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

			'primary'		=> ['name', 'teacher_id'],

			'autoincrement' => null,

			'nullable'		=> ['phone'],

			'uniques'		=> [],

			'rules' 		=> [
				'name' => ['type' => 'str', 'max' => 50, 'required' => true],
				'teacher_id' => ['type' => 'int', 'required' => true],
				'phone' => ['type' => 'str', 'max' => 20]
			],

			'fks' 			=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}


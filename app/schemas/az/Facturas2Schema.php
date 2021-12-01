<?php

namespace simplerest\schemas\az;

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

			'primary'		=> ['id', 'co'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'lastname'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'co' => ['type' => 'str', 'max' => 30, 'required' => true],
				'edad' => ['type' => 'int', 'min' => 0, 'required' => true],
				'firstname' => ['type' => 'str', 'max' => 60, 'required' => true],
				'lastname' => ['type' => 'str', 'max' => 60],
				'username' => ['type' => 'str', 'max' => 60, 'required' => true]
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


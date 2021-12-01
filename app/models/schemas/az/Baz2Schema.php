<?php

namespace simplerest\models\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class Baz2Schema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'baz2',

			'id_name'		=> 'id_baz2',

			'attr_types'	=> [
				'id_baz2' => 'INT',
				'name' => 'STR',
				'cost' => 'STR'
			],

			'primary'		=> ['id_baz2'],

			'autoincrement' => 'id_baz2',

			'nullable'		=> ['id_baz2'],

			'uniques'		=> [],

			'rules' 		=> [
				'id_baz2' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 45, 'required' => true],
				'cost' => ['type' => 'decimal(5,2)', 'required' => true]
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


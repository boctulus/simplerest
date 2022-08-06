<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class EmailsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'emails',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'text' => 'STR',
				'confirmed' => 'INT',
				'user_id' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => null,

			'nullable'		=> ['confirmed'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'required' => true],
				'text' => ['type' => 'str', 'max' => 60, 'required' => true],
				'confirmed' => ['type' => 'bool'],
				'user_id' => ['type' => 'int', 'required' => true]
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


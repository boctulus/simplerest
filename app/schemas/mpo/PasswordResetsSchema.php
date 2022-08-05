<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class PasswordResetsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'password_resets',

			'id_name'		=> null,

			'attr_types'	=> [
				'email' => 'STR',
				'token' => 'STR',
				'created_at' => 'STR'
			],

			'primary'		=> [],

			'autoincrement' => null,

			'nullable'		=> ['created_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'email' => ['type' => 'str', 'max' => 255, 'required' => true],
				'token' => ['type' => 'str', 'max' => 255, 'required' => true],
				'created_at' => ['type' => 'timestamp']
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


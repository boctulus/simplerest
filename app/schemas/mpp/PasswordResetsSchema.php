<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class PasswordResetsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'password_resets',

			'id_name'		=> null,

			'fields'		=> ['email', 'token', 'created_at'],

			'attr_types'	=> [
				'email' => 'STR',
				'token' => 'STR',
				'created_at' => 'STR'
			],

			'primary'		=> [],

			'autoincrement' => null,

			'nullable'		=> ['created_at'],

			'required'		=> ['email', 'token'],

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


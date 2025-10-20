<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class PasswordResetTokensSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'password_reset_tokens',

			'id_name'			=> 'email',

			'fields'			=> ['email', 'token', 'created_at'],

			'attr_types'		=> [
				'email' => 'STR',
				'token' => 'STR',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['email'],

			'autoincrement' 	=> null,

			'nullable'			=> ['created_at'],

			'required'			=> ['email', 'token'],

			'uniques'			=> [],

			'rules' 			=> [
				'email' => ['type' => 'str', 'max' => 255, 'required' => true],
				'token' => ['type' => 'str', 'max' => 255, 'required' => true],
				'created_at' => ['type' => 'timestamp']
			],

			'fks' 				=> [],

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


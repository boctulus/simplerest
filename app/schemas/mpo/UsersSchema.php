<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'users',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'name', 'email', 'email_verified_at', 'password', 'remember_token', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR',
				'email' => 'STR',
				'email_verified_at' => 'STR',
				'password' => 'STR',
				'remember_token' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'email_verified_at', 'remember_token', 'created_at', 'updated_at'],

			'required'		=> ['name', 'email', 'password'],

			'uniques'		=> ['email'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'name' => ['type' => 'str', 'max' => 255, 'required' => true],
				'email' => ['type' => 'str', 'max' => 255, 'required' => true],
				'email_verified_at' => ['type' => 'timestamp'],
				'password' => ['type' => 'str', 'max' => 255, 'required' => true],
				'remember_token' => ['type' => 'str', 'max' => 100],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
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


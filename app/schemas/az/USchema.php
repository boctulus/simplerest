<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class USchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'u',

			'id_name'		=> 'id_u',

			'attr_types'	=> [
				'id_u' => 'INT',
				'username' => 'STR',
				'is_active' => 'INT',
				'is_locked' => 'INT',
				'email' => 'STR',
				'confirmed_email' => 'INT',
				'firstname' => 'STR',
				'lastname' => 'STR',
				'password' => 'STR',
				'u_settings_id' => 'INT',
				'deleted_at' => 'STR'
			],

			'primary'		=> ['id_u'],

			'autoincrement' => 'id_u',

			'nullable'		=> ['id_u', 'is_active', 'is_locked', 'confirmed_email', 'firstname', 'lastname', 'password', 'deleted_at'],

			'uniques'		=> ['username', 'email', 'u_settings_id'],

			'rules' 		=> [
				'id_u' => ['type' => 'int'],
				'username' => ['type' => 'str', 'max' => 15, 'required' => true],
				'is_active' => ['type' => 'bool'],
				'is_locked' => ['type' => 'bool'],
				'email' => ['type' => 'str', 'max' => 60, 'required' => true],
				'confirmed_email' => ['type' => 'bool'],
				'firstname' => ['type' => 'str', 'max' => 50],
				'lastname' => ['type' => 'str', 'max' => 80],
				'password' => ['type' => 'str', 'max' => 60],
				'u_settings_id' => ['type' => 'int', 'required' => true],
				'deleted_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['u_settings_id'],

			'relationships' => [
				'u_settings' => [
					['u_settings.id_us','u.u_settings_id']
				]
			],

			'expanded_relationships' => array (
				  'u_settings' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'u_settings',
				        1 => 'id_us',
				      ),
				      1 => 
				      array (
				        0 => 'u',
				        1 => 'u_settings_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'u_settings' => [
					['u_settings.id_us','u.u_settings_id']
				]
			],

			'expanded_relationships_from' => array (
				  'u_settings' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'u_settings',
				        1 => 'id_us',
				      ),
				      1 => 
				      array (
				        0 => 'u',
				        1 => 'u_settings_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}


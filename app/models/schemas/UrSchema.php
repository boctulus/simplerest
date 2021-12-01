<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UrSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'ur',

			'id_name'		=> 'id_ur',

			'attr_types'	=> [
				'id_ur' => 'INT',
				'username' => 'STR',
				'active' => 'INT',
				'locked' => 'INT',
				'email' => 'STR',
				'confirmed_email' => 'INT',
				'firstname' => 'STR',
				'lastname' => 'STR',
				'password' => 'STR',
				'deleted_at' => 'STR'
			],

			'primary'		=> ['id_ur'],

			'autoincrement' => null,

			'nullable'		=> ['active', 'locked', 'confirmed_email', 'firstname', 'lastname', 'password', 'deleted_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id_ur' => ['type' => 'int', 'required' => true],
				'username' => ['type' => 'str', 'max' => 15, 'required' => true],
				'active' => ['type' => 'bool'],
				'locked' => ['type' => 'bool'],
				'email' => ['type' => 'str', 'max' => 60, 'required' => true],
				'confirmed_email' => ['type' => 'bool'],
				'firstname' => ['type' => 'str', 'max' => 50],
				'lastname' => ['type' => 'str', 'max' => 80],
				'password' => ['type' => 'str', 'max' => 60],
				'deleted_at' => ['type' => 'datetime']
			],

			'fks' 			=> [],

			'relationships' => [
				'ur_settings' => [
					['ur_settings.ur_id','ur.id_ur']
				]
			],

			'expanded_relationships' => array (
				  'ur_settings' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'ur_settings',
				        1 => 'ur_id',
				      ),
				      1 => 
				      array (
				        0 => 'ur',
				        1 => 'id_ur',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}


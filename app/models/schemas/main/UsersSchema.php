<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'users',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'firstname' => 'STR',
				'lastname' => 'STR',
				'username' => 'STR',
				'password' => 'STR',
				'active' => 'INT',
				'locked' => 'INT',
				'email' => 'STR',
				'confirmed_email' => 'INT',
				'belongs_to' => 'INT',
				'created_by' => 'INT',
				'updated_by' => 'INT',
				'deleted_by' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'nullable'		=> ['id', 'firstname', 'lastname', 'password', 'active', 'confirmed_email', 'belongs_to', 'created_by', 'updated_by', 'deleted_by', 'updated_at', 'deleted_at'],

			'rules' 		=> [
				'firstname' => ['max' => 50],
				'lastname' => ['max' => 80],
				'username' => ['max' => 15],
				'password' => ['max' => 60],
				'email' => ['max' => 60]
			],

			'relationships' => [
				'users' => [
					['belongs_tos.id','users.belongs_to'],
					['belongs_toss.id','users.created_by'],
					['belongs_tosss.id','users.updated_by'],
					['belongs_tossss.id','users.deleted_by']
				]
			]
		];
	}	
}


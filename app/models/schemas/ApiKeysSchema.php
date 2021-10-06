<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ApiKeysSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'api_keys',

			'id_name'		=> 'uuid',

			'attr_types'	=> [
				'uuid' => 'STR',
				'value' => 'STR',
				'user_id' => 'INT',
				'created_at' => 'STR'
			],

			'nullable'		=> ['created_at', 'uuid'],

			'rules' 		=> [
				'uuid' => ['type' => 'str', 'max' => 36],
				'value' => ['type' => 'str', 'max' => 60, 'required' => true],
				'user_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime']
			],

			'relationships' => [
				'users' => [
					['users.id','api_keys.user_id']
				]
			]
		];
	}	
}


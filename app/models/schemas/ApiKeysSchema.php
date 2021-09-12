<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ApiKeysSchema implements ISchema
{ 
	### TRAITS
	
	function get(){
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
				'uuid' => ['max' => 36],
				'value' => ['max' => 60]
			],

			'relationships' => [
				'users' => [
					['users.id','api_keys.user_id']
				]
			]
		];
	}	
}


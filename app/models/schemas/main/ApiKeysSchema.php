<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ApiKeysSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'api_keys',

			'id_name'		=> NULL,

			'attr_types'	=> [
				'uuid' => 'STR',
				'value' => 'STR',
				'user_id' => 'INT',
				'created_at' => 'STR'
			],

			'nullable'		=> ['created_at'],

			'rules' 		=> [
				'uuid' => ['max' => 36],
				'value' => ['max' => 60]
			],

			'relationships' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','api_keys.user_id']
				]
			]
		];
	}	
}


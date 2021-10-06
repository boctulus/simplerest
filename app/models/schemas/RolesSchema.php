<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class RolesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'roles',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 50, 'required' => true]
			],

			'relationships' => [
				
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class RolesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'roles',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'STR',
				'name' => 'STR'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'name' => ['max' => 50]
			],

			'relationships' => [
				
			]
		];
	}	
}

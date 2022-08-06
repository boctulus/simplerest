<?php

namespace simplerest\schemas\az;

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

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> ['name'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 50, 'required' => true]
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


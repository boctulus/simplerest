<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class SuperCoolTableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'super_cool_table',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR',
				'age' => 'INT',
				'is_active' => 'INT',
				'belongs_to' => 'INT',
				'deleted_at' => 'STR',
				'is_locked' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'deleted_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 45, 'required' => true],
				'age' => ['type' => 'int', 'required' => true],
				'is_active' => ['type' => 'bool', 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'deleted_at' => ['type' => 'datetime'],
				'is_locked' => ['type' => 'bool', 'required' => true]
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


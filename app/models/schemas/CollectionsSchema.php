<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CollectionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'collections',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'entity' => 'STR',
				'refs' => 'STR',
				'belongs_to' => 'INT',
				'created_at' => 'STR'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'entity' => ['type' => 'str', 'max' => 80, 'required' => true],
				'refs' => ['type' => 'str', 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
			],

			'relationships' => [
				'users' => [
					['users.id','collections.belongs_to']
				]
			]
		];
	}	
}


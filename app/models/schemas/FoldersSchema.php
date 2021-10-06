<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FoldersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'folders',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'tb' => 'STR',
				'name' => 'STR',
				'belongs_to' => 'INT'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'tb' => ['type' => 'str', 'max' => 40, 'required' => true],
				'name' => ['type' => 'str', 'max' => 40, 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'users' => [
					['users.id','folders.belongs_to']
				]
			]
		];
	}	
}


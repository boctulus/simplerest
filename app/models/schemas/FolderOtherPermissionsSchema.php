<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FolderOtherPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'folder_other_permissions',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'folder_id' => 'INT',
				'belongs_to' => 'INT',
				'guest' => 'INT',
				'r' => 'INT',
				'w' => 'INT'
			],

			'nullable'		=> ['id', 'guest'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'folder_id' => ['type' => 'int', 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'guest' => ['type' => 'bool'],
				'r' => ['type' => 'bool', 'required' => true],
				'w' => ['type' => 'bool', 'required' => true]
			],

			'relationships' => [
				'users' => [
					['users.id','folder_other_permissions.belongs_to']
				]
			]
		];
	}	
}


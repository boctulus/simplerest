<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FolderPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'folder_permissions',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'folder_id' => 'INT',
				'belongs_to' => 'INT',
				'access_to' => 'INT',
				'r' => 'INT',
				'w' => 'INT'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'folder_id' => ['type' => 'int', 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'access_to' => ['type' => 'int', 'required' => true],
				'r' => ['type' => 'bool', 'required' => true],
				'w' => ['type' => 'bool', 'required' => true]
			],

			'relationships' => [
				'users' => [
					['users.id','folder_permissions.belongs_to']
				]
			]
		];
	}	
}


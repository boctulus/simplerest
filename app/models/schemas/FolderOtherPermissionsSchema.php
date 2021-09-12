<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FolderOtherPermissionsSchema implements ISchema
{ 
	### TRAITS
	
	function get(){
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

			'nullable'		=> ['id'],

			'rules' 		=> [

			],

			'relationships' => [
				'users' => [
					['users.id','folder_other_permissions.belongs_to']
				]
			]
		];
	}	
}


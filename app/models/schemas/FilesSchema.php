<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FilesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'files',

			'id_name'		=> 'uuid',

			'attr_types'	=> [
				'uuid' => 'STR',
				'filename' => 'STR',
				'file_ext' => 'STR',
				'filename_as_stored' => 'STR',
				'belongs_to' => 'INT',
				'guest_access' => 'INT',
				'locked' => 'INT',
				'broken' => 'INT',
				'created_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'nullable'		=> ['belongs_to', 'guest_access', 'locked', 'broken', 'deleted_at', 'uuid'],

			'rules' 		=> [
				'uuid' => ['type' => 'str', 'max' => 36],
				'filename' => ['type' => 'str', 'max' => 255, 'required' => true],
				'file_ext' => ['type' => 'str', 'max' => 30, 'required' => true],
				'filename_as_stored' => ['type' => 'str', 'max' => 60, 'required' => true],
				'belongs_to' => ['type' => 'int'],
				'guest_access' => ['type' => 'bool'],
				'locked' => ['type' => 'bool'],
				'broken' => ['type' => 'bool'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'deleted_at' => ['type' => 'datetime']
			],

			'relationships' => [
				'users' => [
					['users.id','files.belongs_to']
				]
			]
		];
	}	
}


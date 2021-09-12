<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FilesSchema implements ISchema
{ 
	### TRAITS
	
	function get(){
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

			'nullable'		=> ['belongs_to', 'guest_access', 'broken', 'deleted_at', 'uuid'],

			'rules' 		=> [
				'uuid' => ['max' => 36],
				'filename' => ['max' => 255],
				'file_ext' => ['max' => 30],
				'filename_as_stored' => ['max' => 60]
			],

			'relationships' => [
				'users' => [
					['users.id','files.belongs_to']
				]
			]
		];
	}	
}


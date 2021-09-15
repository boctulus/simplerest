<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FolderPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'folder_permissions',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'STR',
				'folder_id' => 'INT',
				'belongs_to' => 'INT',
				'access_to' => 'INT',
				'r' => 'INT',
				'w' => 'INT',
				'created_at' => 'STR'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [

			],

			'relationships' => [
				'tbl_usuario_empresa' => [
					['belongs_tos.use_intId','folder_permissions.belongs_to'],
					['belongs_toss.use_intId','folder_permissions.access_to']
				]
			]
		];
	}	
}


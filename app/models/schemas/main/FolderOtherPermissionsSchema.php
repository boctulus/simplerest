<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FolderOtherPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'folder_other_permissions',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'STR',
				'folder_id' => 'INT',
				'belongs_to' => 'INT',
				'guest' => 'INT',
				'r' => 'INT',
				'w' => 'INT',
				'created_at' => 'STR'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [

			],

			'relationships' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','folder_other_permissions.belongs_to']
				]
			]
		];
	}	
}


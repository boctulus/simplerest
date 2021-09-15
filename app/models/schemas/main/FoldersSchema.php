<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FoldersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'folders',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'STR',
				'tb' => 'STR',
				'name' => 'STR',
				'belongs_to' => 'INT',
				'created_at' => 'STR'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'tb' => ['max' => 50],
				'name' => ['max' => 50]
			],

			'relationships' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','folders.belongs_to']
				]
			]
		];
	}	
}


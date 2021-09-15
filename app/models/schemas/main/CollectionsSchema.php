<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CollectionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'collections',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'STR',
				'entity' => 'STR',
				'refs' => 'STR',
				'belongs_to' => 'INT',
				'created_at' => 'STR'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'entity' => ['max' => 80]
			],

			'relationships' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','collections.belongs_to']
				]
			]
		];
	}	
}


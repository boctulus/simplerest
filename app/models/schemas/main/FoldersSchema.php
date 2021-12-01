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
				'id' => 'INT',
				'tb' => 'STR',
				'name' => 'STR',
				'belongs_to' => 'INT',
				'created_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'tb' => ['type' => 'str', 'max' => 50, 'required' => true],
				'name' => ['type' => 'str', 'max' => 50, 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
			],

			'fks' 			=> ['belongs_to'],

			'relationships' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','folders.belongs_to']
				]
			],

			'expanded_relationships' => array (
				  'tbl_usuario_empresa' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				      1 => 
				      array (
				        0 => 'folders',
				        1 => 'belongs_to',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','folders.belongs_to']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_usuario_empresa' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				      1 => 
				      array (
				        0 => 'folders',
				        1 => 'belongs_to',
				      ),
				    ),
				  ),
				)
		];
	}	
}


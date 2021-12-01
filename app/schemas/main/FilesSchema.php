<?php

namespace simplerest\schemas\main;

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

			'primary'		=> ['uuid'],

			'autoincrement' => null,

			'nullable'		=> ['belongs_to', 'guest_access', 'locked', 'broken', 'deleted_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'uuid' => ['type' => 'str', 'max' => 60, 'required' => true],
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

			'fks' 			=> ['belongs_to'],

			'relationships' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','files.belongs_to']
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
				        0 => 'files',
				        1 => 'belongs_to',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa.use_intId','files.belongs_to']
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
				        0 => 'files',
				        1 => 'belongs_to',
				      ),
				    ),
				  ),
				)
		];
	}	
}


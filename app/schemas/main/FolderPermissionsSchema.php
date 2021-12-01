<?php

namespace simplerest\schemas\main;

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
				'w' => 'INT',
				'created_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'folder_id' => ['type' => 'int', 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'access_to' => ['type' => 'int', 'required' => true],
				'r' => ['type' => 'bool', 'required' => true],
				'w' => ['type' => 'bool', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
			],

			'fks' 			=> ['belongs_to', 'access_to'],

			'relationships' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa|__belongs_to.use_intId','folder_permissions.belongs_to'],
					['tbl_usuario_empresa|__access_to.use_intId','folder_permissions.access_to']
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
				        'alias' => '__belongs_to',
				      ),
				      1 => 
				      array (
				        0 => 'folder_permissions',
				        1 => 'belongs_to',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				        'alias' => '__access_to',
				      ),
				      1 => 
				      array (
				        0 => 'folder_permissions',
				        1 => 'access_to',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_usuario_empresa' => [
					['tbl_usuario_empresa|__belongs_to.use_intId','folder_permissions.belongs_to'],
					['tbl_usuario_empresa|__access_to.use_intId','folder_permissions.access_to']
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
				        'alias' => '__belongs_to',
				      ),
				      1 => 
				      array (
				        0 => 'folder_permissions',
				        1 => 'belongs_to',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				        'alias' => '__access_to',
				      ),
				      1 => 
				      array (
				        0 => 'folder_permissions',
				        1 => 'access_to',
				      ),
				    ),
				  ),
				)
		];
	}	
}


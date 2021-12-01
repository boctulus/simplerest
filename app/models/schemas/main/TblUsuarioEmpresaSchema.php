<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblUsuarioEmpresaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_usuario_empresa',

			'id_name'		=> 'use_intId',

			'attr_types'	=> [
				'use_intId' => 'INT',
				'use_varNombre' => 'STR',
				'use_varEmail' => 'STR',
				'est_intIdConfirmEmail' => 'INT',
				'use_varUsuario' => 'STR',
				'use_decPassword' => 'STR',
				'use_varTipo' => 'STR',
				'use_dtimFechaCreacion' => 'STR',
				'use_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['use_intId'],

			'autoincrement' => 'use_intId',

			'nullable'		=> ['use_intId', 'use_varNombre', 'est_intIdConfirmEmail', 'use_varTipo', 'use_dtimFechaCreacion', 'use_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'use_intId' => ['type' => 'int'],
				'use_varNombre' => ['type' => 'str', 'max' => 250],
				'use_varEmail' => ['type' => 'str', 'max' => 100, 'required' => true],
				'est_intIdConfirmEmail' => ['type' => 'bool'],
				'use_varUsuario' => ['type' => 'str', 'max' => 250, 'required' => true],
				'use_decPassword' => ['type' => 'str', 'max' => 100, 'required' => true],
				'use_varTipo' => ['type' => 'str', 'max' => 100],
				'use_dtimFechaCreacion' => ['type' => 'datetime'],
				'use_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> [],

			'relationships' => [
				'folders' => [
					['folders.belongs_to','tbl_usuario_empresa.use_intId']
				],
				'files' => [
					['files.belongs_to','tbl_usuario_empresa.use_intId']
				],
				'user_roles' => [
					['user_roles.user_id','tbl_usuario_empresa.use_intId']
				],
				'folder_other_permissions' => [
					['folder_other_permissions.belongs_to','tbl_usuario_empresa.use_intId']
				],
				'api_keys' => [
					['api_keys.user_id','tbl_usuario_empresa.use_intId']
				],
				'user_tb_permissions' => [
					['user_tb_permissions.user_id','tbl_usuario_empresa.use_intId']
				],
				'user_sp_permissions' => [
					['user_sp_permissions.user_id','tbl_usuario_empresa.use_intId']
				],
				'folder_permissions' => [
					['folder_permissions.belongs_to','tbl_usuario_empresa.use_intId'],
					['folder_permissions.access_to','tbl_usuario_empresa.use_intId']
				],
				'collections' => [
					['collections.belongs_to','tbl_usuario_empresa.use_intId']
				]
			],

			'expanded_relationships' => array (
				  'folders' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'folders',
				        1 => 'belongs_to',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				  ),
				  'files' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'files',
				        1 => 'belongs_to',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				  ),
				  'user_roles' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'user_roles',
				        1 => 'user_id',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				  ),
				  'folder_other_permissions' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'folder_other_permissions',
				        1 => 'belongs_to',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				  ),
				  'api_keys' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'api_keys',
				        1 => 'user_id',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				  ),
				  'user_tb_permissions' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'user_tb_permissions',
				        1 => 'user_id',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				  ),
				  'user_sp_permissions' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'user_sp_permissions',
				        1 => 'user_id',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				  ),
				  'folder_permissions' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'folder_permissions',
				        1 => 'belongs_to',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'folder_permissions',
				        1 => 'access_to',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				  ),
				  'collections' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'collections',
				        1 => 'belongs_to',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_usuario_empresa',
				        1 => 'use_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}


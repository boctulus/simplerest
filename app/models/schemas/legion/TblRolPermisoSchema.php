<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRolPermisoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_rol_permiso',

			'id_name'		=> 'rpe_intId',

			'attr_types'	=> [
				'rpe_intId' => 'INT',
				'rpe_dtimFechaCreacion' => 'STR',
				'rpe_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'rol_intIdRol' => 'INT',
				'per_intIdPermiso' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['rpe_intId'],

			'autoincrement' => 'rpe_intId',

			'nullable'		=> ['rpe_intId', 'rpe_dtimFechaCreacion', 'rpe_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'rpe_intId' => ['type' => 'int'],
				'rpe_dtimFechaCreacion' => ['type' => 'datetime'],
				'rpe_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'rol_intIdRol' => ['type' => 'int', 'required' => true],
				'per_intIdPermiso' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'per_intIdPermiso', 'rol_intIdRol', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rol_permiso.est_intIdEstado']
				],
				'tbl_permiso' => [
					['tbl_permiso.per_intId','tbl_rol_permiso.per_intIdPermiso']
				],
				'tbl_rol' => [
					['tbl_rol.rol_intId','tbl_rol_permiso.rol_intIdRol']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rol_permiso.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rol_permiso.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_permiso' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_permiso',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'per_intIdPermiso',
				      ),
				    ),
				  ),
				  'tbl_rol' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'rol_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'rol_intIdRol',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rol_permiso.est_intIdEstado']
				],
				'tbl_permiso' => [
					['tbl_permiso.per_intId','tbl_rol_permiso.per_intIdPermiso']
				],
				'tbl_rol' => [
					['tbl_rol.rol_intId','tbl_rol_permiso.rol_intIdRol']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rol_permiso.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rol_permiso.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_permiso' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_permiso',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'per_intIdPermiso',
				      ),
				    ),
				  ),
				  'tbl_rol' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'rol_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'rol_intIdRol',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


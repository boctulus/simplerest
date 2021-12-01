<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRolSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_rol',

			'id_name'		=> 'rol_intId',

			'attr_types'	=> [
				'rol_intId' => 'INT',
				'rol_varNombre' => 'STR',
				'rol_varDescripcion' => 'STR',
				'rol_dtimFechaCreacion' => 'STR',
				'rol_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado_rol' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['rol_intId'],

			'autoincrement' => 'rol_intId',

			'nullable'		=> ['rol_intId', 'rol_dtimFechaCreacion', 'rol_dtimFechaActualizacion', 'est_intIdEstado_rol', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'rol_intId' => ['type' => 'int'],
				'rol_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'rol_varDescripcion' => ['type' => 'str', 'max' => 100, 'required' => true],
				'rol_dtimFechaCreacion' => ['type' => 'datetime'],
				'rol_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado_rol' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado_rol', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rol.est_intIdEstado_rol']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rol.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rol.usu_intIdActualizador'],
					['tbl_usuario.rol_intIdRol','tbl_rol.rol_intId']
				],
				'tbl_rol_permiso' => [
					['tbl_rol_permiso.rol_intIdRol','tbl_rol.rol_intId']
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
				        0 => 'tbl_rol',
				        1 => 'est_intIdEstado_rol',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    2 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'rol_intIdRol',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'rol_intId',
				      ),
				    ),
				  ),
				  'tbl_rol_permiso' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rol_permiso',
				        1 => 'rol_intIdRol',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'rol_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rol.est_intIdEstado_rol']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rol.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rol.usu_intIdActualizador']
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
				        0 => 'tbl_rol',
				        1 => 'est_intIdEstado_rol',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rol',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


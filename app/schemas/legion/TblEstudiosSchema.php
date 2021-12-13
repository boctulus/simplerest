<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEstudiosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_estudios',

			'id_name'		=> 'esd_intId',

			'attr_types'	=> [
				'esd_intId' => 'INT',
				'esd_varCodigo' => 'STR',
				'esd_varNombre' => 'STR',
				'esd_lonDescripcion' => 'STR',
				'esd_dtimFechaCreacion' => 'STR',
				'esd_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['esd_intId'],

			'autoincrement' => 'esd_intId',

			'nullable'		=> ['esd_intId', 'esd_varCodigo', 'esd_lonDescripcion', 'esd_dtimFechaCreacion', 'esd_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'esd_intId' => ['type' => 'int'],
				'esd_varCodigo' => ['type' => 'str', 'max' => 100],
				'esd_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'esd_lonDescripcion' => ['type' => 'str'],
				'esd_dtimFechaCreacion' => ['type' => 'datetime'],
				'esd_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_estudios.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_estudios.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_estudios.usu_intIdCreador']
				],
				'tbl_empleado_datos_personales' => [
					['tbl_empleado_datos_personales.esd_intIdEstudios','tbl_estudios.esd_intId']
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
				        0 => 'tbl_estudios',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_estudios',
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
				        0 => 'tbl_estudios',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_empleado_datos_personales' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'esd_intIdEstudios',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_estudios',
				        1 => 'esd_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_estudios.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_estudios.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_estudios.usu_intIdCreador']
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
				        0 => 'tbl_estudios',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_estudios',
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
				        0 => 'tbl_estudios',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


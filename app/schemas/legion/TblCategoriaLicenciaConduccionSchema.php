<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCategoriaLicenciaConduccionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_categoria_licencia_conduccion',

			'id_name'		=> 'clc_intId',

			'attr_types'	=> [
				'clc_intId' => 'INT',
				'clc_varCodigo' => 'STR',
				'clc_varNombre' => 'STR',
				'clc_lonDescripcion' => 'STR',
				'clc_dtimFechaCreacion' => 'STR',
				'clc_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['clc_intId'],

			'autoincrement' => 'clc_intId',

			'nullable'		=> ['clc_intId', 'clc_varCodigo', 'clc_lonDescripcion', 'clc_dtimFechaCreacion', 'clc_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'clc_intId' => ['type' => 'int'],
				'clc_varCodigo' => ['type' => 'str', 'max' => 100],
				'clc_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'clc_lonDescripcion' => ['type' => 'str'],
				'clc_dtimFechaCreacion' => ['type' => 'datetime'],
				'clc_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_licencia_conduccion.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_licencia_conduccion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_licencia_conduccion.usu_intIdCreador']
				],
				'tbl_empleado_datos_personales' => [
					['tbl_empleado_datos_personales.clc_intIdCategoriaLicencia','tbl_categoria_licencia_conduccion.clc_intId']
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
				        0 => 'tbl_categoria_licencia_conduccion',
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
				        0 => 'tbl_categoria_licencia_conduccion',
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
				        0 => 'tbl_categoria_licencia_conduccion',
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
				        1 => 'clc_intIdCategoriaLicencia',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_categoria_licencia_conduccion',
				        1 => 'clc_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_licencia_conduccion.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_licencia_conduccion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_licencia_conduccion.usu_intIdCreador']
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
				        0 => 'tbl_categoria_licencia_conduccion',
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
				        0 => 'tbl_categoria_licencia_conduccion',
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
				        0 => 'tbl_categoria_licencia_conduccion',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


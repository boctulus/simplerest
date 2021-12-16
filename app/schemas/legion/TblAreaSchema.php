<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblAreaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_area',

			'id_name'		=> 'are_intId',

			'attr_types'	=> [
				'are_intId' => 'INT',
				'are_varCodigo' => 'STR',
				'are_varNombre' => 'STR',
				'are_lonDescripcion' => 'STR',
				'are_dtimFechaCreacion' => 'STR',
				'are_dtimFechaActualizacion' => 'STR',
				'emn_intIdEmpresaNomina' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['are_intId'],

			'autoincrement' => 'are_intId',

			'nullable'		=> ['are_intId', 'are_varCodigo', 'are_lonDescripcion', 'are_dtimFechaCreacion', 'are_dtimFechaActualizacion', 'emn_intIdEmpresaNomina', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'are_intId' => ['type' => 'int'],
				'are_varCodigo' => ['type' => 'str', 'max' => 100],
				'are_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'are_lonDescripcion' => ['type' => 'str'],
				'are_dtimFechaCreacion' => ['type' => 'datetime'],
				'are_dtimFechaActualizacion' => ['type' => 'datetime'],
				'emn_intIdEmpresaNomina' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['emn_intIdEmpresaNomina', 'est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.emn_intId','tbl_area.emn_intIdEmpresaNomina']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_area.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_area.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_area.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_empresa_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'emn_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_area',
				        1 => 'emn_intIdEmpresaNomina',
				      ),
				    ),
				  ),
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
				        0 => 'tbl_area',
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
				        0 => 'tbl_area',
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
				        0 => 'tbl_area',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.emn_intId','tbl_area.emn_intIdEmpresaNomina']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_area.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_area.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_area.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_empresa_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'emn_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_area',
				        1 => 'emn_intIdEmpresaNomina',
				      ),
				    ),
				  ),
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
				        0 => 'tbl_area',
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
				        0 => 'tbl_area',
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
				        0 => 'tbl_area',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblSedeSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_sede',

			'id_name'		=> 'sed_intId',

			'attr_types'	=> [
				'sed_intId' => 'INT',
				'sed_varCodigo' => 'STR',
				'sed_varNombre' => 'STR',
				'sed_lonDescripcion' => 'STR',
				'sed_dtimFechaCreacion' => 'STR',
				'sed_dtimFechaActualizacion' => 'STR',
				'emn_intIdEmpresa' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['sed_intId'],

			'autoincrement' => 'sed_intId',

			'nullable'		=> ['sed_intId', 'sed_varCodigo', 'sed_lonDescripcion', 'sed_dtimFechaCreacion', 'sed_dtimFechaActualizacion', 'emn_intIdEmpresa', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'sed_intId' => ['type' => 'int'],
				'sed_varCodigo' => ['type' => 'str', 'max' => 100],
				'sed_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'sed_lonDescripcion' => ['type' => 'str'],
				'sed_dtimFechaCreacion' => ['type' => 'datetime'],
				'sed_dtimFechaActualizacion' => ['type' => 'datetime'],
				'emn_intIdEmpresa' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['emn_intIdEmpresa', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.emn_intId','tbl_sede.emn_intIdEmpresa']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_sede.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_sede.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_sede.usu_intIdCreador']
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
				        0 => 'tbl_sede',
				        1 => 'emn_intIdEmpresa',
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
				        0 => 'tbl_sede',
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
				        0 => 'tbl_sede',
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
				        0 => 'tbl_sede',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.emn_intId','tbl_sede.emn_intIdEmpresa']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_sede.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_sede.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_sede.usu_intIdCreador']
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
				        0 => 'tbl_sede',
				        1 => 'emn_intIdEmpresa',
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
				        0 => 'tbl_sede',
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
				        0 => 'tbl_sede',
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
				        0 => 'tbl_sede',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEmpresaNominaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_empresa_nomina',

			'id_name'		=> 'emn_intId',

			'attr_types'	=> [
				'emn_intId' => 'INT',
				'emn_varRazonSocial' => 'STR',
				'emn_varNit' => 'STR',
				'emn_varEmail' => 'STR',
				'emn_varCelular' => 'STR',
				'emn_varTipoCuenta' => 'STR',
				'emn_varNumeroCuenta' => 'STR',
				'emn_varPila' => 'STR',
				'emn_intAnoConstitucion' => 'INT',
				'emn_tinAplicaLey14292021' => 'INT',
				'emn_tinAplicaLey5902000' => 'INT',
				'emn_tinAporteParas16072012' => 'INT',
				'emn_tinDecreto5582020' => 'INT',
				'emn_dtimFechaCreacion' => 'STR',
				'emn_dtimFechaActualizacion' => 'STR',
				'arl_intIdArl' => 'INT',
				'per_intIdOperador' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['emn_intId'],

			'autoincrement' => 'emn_intId',

			'nullable'		=> ['emn_intId', 'emn_intAnoConstitucion', 'emn_tinAplicaLey14292021', 'emn_tinAplicaLey5902000', 'emn_tinAporteParas16072012', 'emn_tinDecreto5582020', 'emn_dtimFechaCreacion', 'emn_dtimFechaActualizacion', 'arl_intIdArl', 'per_intIdOperador', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'emn_intId' => ['type' => 'int'],
				'emn_varRazonSocial' => ['type' => 'str', 'max' => 300, 'required' => true],
				'emn_varNit' => ['type' => 'str', 'max' => 20, 'required' => true],
				'emn_varEmail' => ['type' => 'str', 'max' => 100, 'required' => true],
				'emn_varCelular' => ['type' => 'str', 'max' => 50, 'required' => true],
				'emn_varTipoCuenta' => ['type' => 'str', 'max' => 20, 'required' => true],
				'emn_varNumeroCuenta' => ['type' => 'str', 'max' => 50, 'required' => true],
				'emn_varPila' => ['type' => 'str', 'max' => 350, 'required' => true],
				'emn_intAnoConstitucion' => ['type' => 'int'],
				'emn_tinAplicaLey14292021' => ['type' => 'bool'],
				'emn_tinAplicaLey5902000' => ['type' => 'bool'],
				'emn_tinAporteParas16072012' => ['type' => 'bool'],
				'emn_tinDecreto5582020' => ['type' => 'bool'],
				'emn_dtimFechaCreacion' => ['type' => 'datetime'],
				'emn_dtimFechaActualizacion' => ['type' => 'datetime'],
				'arl_intIdArl' => ['type' => 'int'],
				'per_intIdOperador' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['arl_intIdArl', 'est_intIdEstado', 'per_intIdOperador', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_arl' => [
					['tbl_arl.arl_intId','tbl_empresa_nomina.arl_intIdArl']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empresa_nomina.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_empresa_nomina.per_intIdOperador']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_empresa_nomina.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_empresa_nomina.usu_intIdCreador']
				],
				'tbl_sede' => [
					['tbl_sede.emn_intIdEmpresa','tbl_empresa_nomina.emn_intId']
				],
				'tbl_cargo' => [
					['tbl_cargo.emn_intIdEmpresa','tbl_empresa_nomina.emn_intId']
				],
				'tbl_area' => [
					['tbl_area.emn_intIdEmpresaNomina','tbl_empresa_nomina.emn_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_arl' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_arl',
				        1 => 'arl_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'arl_intIdArl',
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
				        0 => 'tbl_empresa_nomina',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'per_intIdOperador',
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
				        0 => 'tbl_empresa_nomina',
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
				        0 => 'tbl_empresa_nomina',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_sede' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sede',
				        1 => 'emn_intIdEmpresa',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'emn_intId',
				      ),
				    ),
				  ),
				  'tbl_cargo' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cargo',
				        1 => 'emn_intIdEmpresa',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'emn_intId',
				      ),
				    ),
				  ),
				  'tbl_area' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_area',
				        1 => 'emn_intIdEmpresaNomina',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'emn_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_arl' => [
					['tbl_arl.arl_intId','tbl_empresa_nomina.arl_intIdArl']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empresa_nomina.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_empresa_nomina.per_intIdOperador']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_empresa_nomina.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_empresa_nomina.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_arl' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_arl',
				        1 => 'arl_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'arl_intIdArl',
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
				        0 => 'tbl_empresa_nomina',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'per_intIdOperador',
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
				        0 => 'tbl_empresa_nomina',
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
				        0 => 'tbl_empresa_nomina',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


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
				'emn_varNumeroCuenta' => 'STR',
				'emn_intAnoConstitucion' => 'INT',
				'emn_tinAplicaLey14292021' => 'INT',
				'emn_tinAplicaLey5902000' => 'INT',
				'emn_tinAporteParas16072012' => 'INT',
				'emn_tinDecreto5582020' => 'INT',
				'emn_dtimFechaCreacion' => 'STR',
				'emn_dtimFechaActualizacion' => 'STR',
				'tcb_intIdTipoCuenta' => 'INT',
				'tip_intIdTipoDocumento' => 'INT',
				'ban_intIdBanco' => 'INT',
				'tmp_intIdMedioPago' => 'INT',
				'fre_intIdFrecuencia' => 'INT',
				'arl_intIdArl' => 'INT',
				'opp_intIdOperador' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['emn_intId'],

			'autoincrement' => 'emn_intId',

			'nullable'		=> ['emn_intId', 'emn_intAnoConstitucion', 'emn_tinAplicaLey14292021', 'emn_tinAplicaLey5902000', 'emn_tinAporteParas16072012', 'emn_tinDecreto5582020', 'emn_dtimFechaCreacion', 'emn_dtimFechaActualizacion', 'tcb_intIdTipoCuenta', 'tip_intIdTipoDocumento', 'ban_intIdBanco', 'tmp_intIdMedioPago', 'fre_intIdFrecuencia', 'arl_intIdArl', 'opp_intIdOperador', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'emn_intId' => ['type' => 'int'],
				'emn_varRazonSocial' => ['type' => 'str', 'max' => 300, 'required' => true],
				'emn_varNit' => ['type' => 'str', 'max' => 20, 'required' => true],
				'emn_varEmail' => ['type' => 'str', 'max' => 100, 'required' => true],
				'emn_varCelular' => ['type' => 'str', 'max' => 50, 'required' => true],
				'emn_varNumeroCuenta' => ['type' => 'str', 'max' => 50, 'required' => true],
				'emn_intAnoConstitucion' => ['type' => 'int'],
				'emn_tinAplicaLey14292021' => ['type' => 'bool'],
				'emn_tinAplicaLey5902000' => ['type' => 'bool'],
				'emn_tinAporteParas16072012' => ['type' => 'bool'],
				'emn_tinDecreto5582020' => ['type' => 'bool'],
				'emn_dtimFechaCreacion' => ['type' => 'datetime'],
				'emn_dtimFechaActualizacion' => ['type' => 'datetime'],
				'tcb_intIdTipoCuenta' => ['type' => 'int'],
				'tip_intIdTipoDocumento' => ['type' => 'int'],
				'ban_intIdBanco' => ['type' => 'int'],
				'tmp_intIdMedioPago' => ['type' => 'int'],
				'fre_intIdFrecuencia' => ['type' => 'int'],
				'arl_intIdArl' => ['type' => 'int'],
				'opp_intIdOperador' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['arl_intIdArl', 'ban_intIdBanco', 'est_intIdEstado', 'fre_intIdFrecuencia', 'opp_intIdOperador', 'tcb_intIdTipoCuenta', 'tip_intIdTipoDocumento', 'tmp_intIdMedioPago', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_arl' => [
					['tbl_arl.arl_intId','tbl_empresa_nomina.arl_intIdArl']
				],
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_empresa_nomina.ban_intIdBanco']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empresa_nomina.est_intIdEstado']
				],
				'tbl_frecuencia' => [
					['tbl_frecuencia.fre_intId','tbl_empresa_nomina.fre_intIdFrecuencia']
				],
				'tbl_operador_pila' => [
					['tbl_operador_pila.opp_intId','tbl_empresa_nomina.opp_intIdOperador']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.tcb_intId','tbl_empresa_nomina.tcb_intIdTipoCuenta']
				],
				'tbl_tipo_documento' => [
					['tbl_tipo_documento.tid_intId','tbl_empresa_nomina.tip_intIdTipoDocumento']
				],
				'tbl_medio_pago' => [
					['tbl_medio_pago.tmp_intId','tbl_empresa_nomina.tmp_intIdMedioPago']
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
				  'tbl_banco' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'ban_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'ban_intIdBanco',
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
				  'tbl_frecuencia' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_frecuencia',
				        1 => 'fre_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'fre_intIdFrecuencia',
				      ),
				    ),
				  ),
				  'tbl_operador_pila' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'opp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'opp_intIdOperador',
				      ),
				    ),
				  ),
				  'tbl_tipo_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'tcb_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'tcb_intIdTipoCuenta',
				      ),
				    ),
				  ),
				  'tbl_tipo_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_documento',
				        1 => 'tid_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'tip_intIdTipoDocumento',
				      ),
				    ),
				  ),
				  'tbl_medio_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_medio_pago',
				        1 => 'tmp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'tmp_intIdMedioPago',
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
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_empresa_nomina.ban_intIdBanco']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empresa_nomina.est_intIdEstado']
				],
				'tbl_frecuencia' => [
					['tbl_frecuencia.fre_intId','tbl_empresa_nomina.fre_intIdFrecuencia']
				],
				'tbl_operador_pila' => [
					['tbl_operador_pila.opp_intId','tbl_empresa_nomina.opp_intIdOperador']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.tcb_intId','tbl_empresa_nomina.tcb_intIdTipoCuenta']
				],
				'tbl_tipo_documento' => [
					['tbl_tipo_documento.tid_intId','tbl_empresa_nomina.tip_intIdTipoDocumento']
				],
				'tbl_medio_pago' => [
					['tbl_medio_pago.tmp_intId','tbl_empresa_nomina.tmp_intIdMedioPago']
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
				  'tbl_banco' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'ban_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'ban_intIdBanco',
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
				  'tbl_frecuencia' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_frecuencia',
				        1 => 'fre_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'fre_intIdFrecuencia',
				      ),
				    ),
				  ),
				  'tbl_operador_pila' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'opp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'opp_intIdOperador',
				      ),
				    ),
				  ),
				  'tbl_tipo_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'tcb_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'tcb_intIdTipoCuenta',
				      ),
				    ),
				  ),
				  'tbl_tipo_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_documento',
				        1 => 'tid_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'tip_intIdTipoDocumento',
				      ),
				    ),
				  ),
				  'tbl_medio_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_medio_pago',
				        1 => 'tmp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'tmp_intIdMedioPago',
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


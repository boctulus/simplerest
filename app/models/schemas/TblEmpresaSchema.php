<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEmpresaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_empresa',

			'id_name'		=> 'emp_intId',

			'attr_types'	=> [
				'emp_intId' => 'INT',
				'emp_varRazonSocial' => 'STR',
				'emp_varNit' => 'STR',
				'emp_varEmail' => 'STR',
				'emp_varCelular' => 'STR',
				'emp_varTipoCuenta' => 'STR',
				'emp_varNumeroCuenta' => 'STR',
				'emp_varPila' => 'STR',
				'emp_intAnoConstitucion' => 'INT',
				'emp_bolAplicarLey14292020' => 'INT',
				'emp_bolAplicarLey5902000' => 'INT',
				'emp_bolAportaParafiscales16072012' => 'INT',
				'emp_bolAplicaDecreto5582000' => 'INT',
				'emp_dtimFechaCreacion' => 'STR',
				'emp_dtimFechaActualizacion' => 'STR',
				'arl_intIdArl' => 'INT',
				'opp_intIdOperador' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['emp_intId', 'emp_intAnoConstitucion', 'emp_bolAplicarLey14292020', 'emp_bolAplicarLey5902000', 'emp_bolAportaParafiscales16072012', 'emp_bolAplicaDecreto5582000', 'emp_dtimFechaCreacion', 'emp_dtimFechaActualizacion', 'arl_intIdArl', 'opp_intIdOperador', 'est_intIdEstado'],

			'rules' 		=> [
				'emp_intId' => ['type' => 'int'],
				'emp_varRazonSocial' => ['type' => 'str', 'max' => 300, 'required' => true],
				'emp_varNit' => ['type' => 'str', 'max' => 20, 'required' => true],
				'emp_varEmail' => ['type' => 'str', 'max' => 100, 'required' => true],
				'emp_varCelular' => ['type' => 'str', 'max' => 50, 'required' => true],
				'emp_varTipoCuenta' => ['type' => 'str', 'max' => 20, 'required' => true],
				'emp_varNumeroCuenta' => ['type' => 'str', 'max' => 50, 'required' => true],
				'emp_varPila' => ['type' => 'str', 'max' => 350, 'required' => true],
				'emp_intAnoConstitucion' => ['type' => 'int'],
				'emp_bolAplicarLey14292020' => ['type' => 'bool'],
				'emp_bolAplicarLey5902000' => ['type' => 'bool', 'min' => 0],
				'emp_bolAportaParafiscales16072012' => ['type' => 'bool'],
				'emp_bolAplicaDecreto5582000' => ['type' => 'bool'],
				'emp_dtimFechaCreacion' => ['type' => 'datetime'],
				'emp_dtimFechaActualizacion' => ['type' => 'datetime'],
				'arl_intIdArl' => ['type' => 'int'],
				'opp_intIdOperador' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_arl' => [
					['tbl_arl.arl_intId','tbl_empresa.arl_intIdArl']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empresa.est_intIdEstado']
				],
				'tbl_operador_pila' => [
					['tbl_operador_pila.opp_intId','tbl_empresa.opp_intIdOperador']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_empresa.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_empresa.usu_intIdCreador']
				],
				'tbl_contacto' => [
					['tbl_contacto.emp_intIdEmpresa','tbl_empresa.emp_intId']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.emp_intIdEmpresa','tbl_empresa.emp_intId']
				]
			]
		];
	}	
}

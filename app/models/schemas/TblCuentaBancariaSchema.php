<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCuentaBancariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cuenta_bancaria',

			'id_name'		=> 'cba_intId',

			'attr_types'	=> [
				'cba_intId' => 'INT',
				'cba_varDescripcion' => 'STR',
				'cba_varNumeroCuenta' => 'STR',
				'cba_dtimFechaCreacion' => 'STR',
				'cba_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado_cba' => 'INT',
				'ban_intIdBanco' => 'INT',
				'ccb_intIdCategoriaCuentaBancaria' => 'INT',
				'emp_intIdEmpresa' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['cba_intId', 'cba_dtimFechaCreacion', 'cba_dtimFechaActualizacion', 'est_intIdEstado_cba'],

			'rules' 		=> [
				'cba_intId' => ['type' => 'int'],
				'cba_varDescripcion' => ['type' => 'str', 'max' => 100, 'required' => true],
				'cba_varNumeroCuenta' => ['type' => 'str', 'max' => 11, 'required' => true],
				'cba_dtimFechaCreacion' => ['type' => 'datetime'],
				'cba_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado_cba' => ['type' => 'int'],
				'ban_intIdBanco' => ['type' => 'int', 'required' => true],
				'ccb_intIdCategoriaCuentaBancaria' => ['type' => 'int', 'required' => true],
				'emp_intIdEmpresa' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_cuenta_bancaria.ban_intIdBanco']
				],
				'tbl_categoria_cuenta_bancaria' => [
					['tbl_categoria_cuenta_bancaria.ccb_intId','tbl_cuenta_bancaria.ccb_intIdCategoriaCuentaBancaria']
				],
				'tbl_empresa' => [
					['tbl_empresa.emp_intId','tbl_cuenta_bancaria.emp_intIdEmpresa']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cuenta_bancaria.est_intIdEstado_cba']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cuenta_bancaria.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cuenta_bancaria.usu_intIdActualizador']
				]
			]
		];
	}	
}


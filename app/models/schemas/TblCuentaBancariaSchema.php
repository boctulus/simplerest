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

			'nullable'		=> ['cba_intId'],

			'rules' 		=> [
				'cba_varDescripcion' => ['max' => 100],
				'cba_varNumeroCuenta' => ['max' => 11]
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
					['usu_intIdActualizadors.usu_intId','tbl_cuenta_bancaria.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_cuenta_bancaria.usu_intIdCreador']
				]
			]
		];
	}	
}


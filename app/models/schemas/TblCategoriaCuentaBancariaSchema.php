<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCategoriaCuentaBancariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_categoria_cuenta_bancaria',

			'id_name'		=> 'ccb_intId',

			'attr_types'	=> [
				'ccb_intId' => 'INT',
				'ccb_varCategoriaCuentaBancaria' => 'STR',
				'ccb_dtimFechaCreacion' => 'STR',
				'ccb_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['ccb_intId'],

			'rules' 		=> [
				'ccb_varCategoriaCuentaBancaria' => ['max' => 50]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_cuenta_bancaria.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_categoria_cuenta_bancaria.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_categoria_cuenta_bancaria.usu_intIdCreador']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.ccb_intIdCategoriaCuentaBancaria','tbl_categoria_cuenta_bancaria.ccb_intId']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.ccb_intIdCategoriaCuentaBancaria','tbl_categoria_cuenta_bancaria.ccb_intId']
				]
			]
		];
	}	
}


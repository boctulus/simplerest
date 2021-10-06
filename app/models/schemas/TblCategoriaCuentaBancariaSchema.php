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

			'nullable'		=> ['ccb_intId', 'ccb_dtimFechaCreacion', 'ccb_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'ccb_intId' => ['type' => 'int'],
				'ccb_varCategoriaCuentaBancaria' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ccb_dtimFechaCreacion' => ['type' => 'datetime'],
				'ccb_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_cuenta_bancaria.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_cuenta_bancaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_cuenta_bancaria.usu_intIdCreador']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.ccb_intIdCategoriaCuentaBancaria','tbl_categoria_cuenta_bancaria.ccb_intId']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.ccb_intIdCategoriaCuentaBancaria','tbl_categoria_cuenta_bancaria.ccb_intId']
				]
			]
		];
	}	
}


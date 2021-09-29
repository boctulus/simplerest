<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblProveedorSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_proveedor',

			'id_name'		=> 'prv_intId',

			'attr_types'	=> [
				'prv_intId' => 'INT',
				'pro_intCuentaBancaria' => 'STR',
				'prv_dtimFechaCreacion' => 'STR',
				'prv_dtimFechaActualizacion' => 'STR',
				'dpa_intIdDiasPago' => 'INT',
				'ban_intIdBanco' => 'INT',
				'ccb_intIdCategoriaCuentaBancaria' => 'INT',
				'per_intIdPersona' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['prv_intId', 'per_intIdPersona', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'rules' 		=> [
				'pro_intCuentaBancaria' => ['max' => 15]
			],

			'relationships' => [
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_proveedor.ban_intIdBanco']
				],
				'tbl_categoria_cuenta_bancaria' => [
					['tbl_categoria_cuenta_bancaria.ccb_intId','tbl_proveedor.ccb_intIdCategoriaCuentaBancaria']
				],
				'tbl_dias_pago' => [
					['tbl_dias_pago.dpa_intId','tbl_proveedor.dpa_intIdDiasPago']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_proveedor.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_proveedor.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_proveedor.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_proveedor.usu_intIdActualizador']
				],
				'tbl_proveedor_informacion_tributaria' => [
					['tbl_proveedor_informacion_tributaria.prv_intIdProveedor','tbl_proveedor.prv_intId']
				]
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblDiasPagoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_dias_pago',

			'id_name'		=> 'dpa_intId',

			'attr_types'	=> [
				'dpa_intId' => 'INT',
				'dpa_intDiasPago' => 'INT',
				'dpa_dtimFechaCreacion' => 'STR',
				'dpa_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['dpa_intId', 'dpa_dtimFechaCreacion', 'dpa_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'dpa_intId' => ['type' => 'int'],
				'dpa_intDiasPago' => ['type' => 'int', 'required' => true],
				'dpa_dtimFechaCreacion' => ['type' => 'datetime'],
				'dpa_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_dias_pago.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_dias_pago.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_dias_pago.usu_intIdCreador']
				],
				'tbl_cliente' => [
					['tbl_cliente.dpa_intIdDiasPago','tbl_dias_pago.dpa_intId']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.dpa_intIdDiasPago','tbl_dias_pago.dpa_intId']
				]
			]
		];
	}	
}

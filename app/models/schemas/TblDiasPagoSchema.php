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

			'nullable'		=> ['dpa_intId'],

			'rules' 		=> [

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


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblIvaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_iva',

			'id_name'		=> 'iva_intId',

			'attr_types'	=> [
				'iva_intId' => 'INT',
				'iva_varIVA' => 'STR',
				'iva_intTope' => 'INT',
				'iva_decPorcentaje' => 'STR',
				'iva_dtimFechaCreacion' => 'STR',
				'iva_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT',
				'sub_intIdCuentaContable' => 'INT'
			],

			'nullable'		=> ['iva_intId'],

			'rules' 		=> [
				'iva_varIVA' => ['max' => 50]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_iva.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_iva.sub_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_iva.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_iva.usu_intIdActualizador']
				],
				'tbl_iva_cuentacontable' => [
					['tbl_iva_cuentacontable.ivc_intIdIva','tbl_iva.iva_intId']
				],
				'tbl_producto' => [
					['tbl_producto.iva_intIdIva','tbl_iva.iva_intId']
				]
			]
		];
	}	
}


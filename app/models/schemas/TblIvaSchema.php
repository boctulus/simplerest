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

			'nullable'		=> ['iva_intId', 'iva_dtimFechaCreacion', 'iva_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'iva_decPorcentaje' => ['type' => 'str', 'required' => true],
				'iva_intId' => ['type' => 'int'],
				'iva_varIVA' => ['type' => 'str', 'max' => 50, 'required' => true],
				'iva_intTope' => ['type' => 'int', 'required' => true],
				'iva_dtimFechaCreacion' => ['type' => 'datetime'],
				'iva_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true],
				'sub_intIdCuentaContable' => ['type' => 'int', 'required' => true]
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

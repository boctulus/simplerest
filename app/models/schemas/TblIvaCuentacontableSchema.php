<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblIvaCuentacontableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_iva_cuentacontable',

			'id_name'		=> 'ivc_intId',

			'attr_types'	=> [
				'ivc_intId' => 'INT',
				'ivc_intIdIva' => 'INT',
				'ivc_intIdCuentaContable' => 'INT',
				'ivc_dtimFechaCreacion' => 'STR',
				'ivc_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['ivc_intId', 'ivc_dtimFechaCreacion', 'ivc_dtimFechaActualizacion'],

			'rules' 		=> [
				'ivc_intId' => ['type' => 'int'],
				'ivc_intIdIva' => ['type' => 'int', 'required' => true],
				'ivc_intIdCuentaContable' => ['type' => 'int', 'required' => true],
				'ivc_dtimFechaCreacion' => ['type' => 'datetime'],
				'ivc_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_iva' => [
					['tbl_iva.iva_intId','tbl_iva_cuentacontable.ivc_intIdIva']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_iva_cuentacontable.ivc_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_iva_cuentacontable.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_iva_cuentacontable.usu_intIdActualizador']
				],
				'tbl_llave_impuesto' => [
					['tbl_llave_impuesto.iva_intIdIvaCuentaContable','tbl_iva_cuentacontable.ivc_intId']
				]
			]
		];
	}	
}


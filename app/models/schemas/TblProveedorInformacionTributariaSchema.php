<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblProveedorInformacionTributariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_proveedor_informacion_tributaria',

			'id_name'		=> 'tip_intId',

			'attr_types'	=> [
				'tip_intId' => 'INT',
				'tip_dtimFechaCreacion' => 'STR',
				'tip_dtimFechaActualizacion' => 'STR',
				'sub_intIdSubCuentaContable' => 'INT',
				'prv_intIdProveedor' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['tip_intId', 'tip_dtimFechaCreacion', 'tip_dtimFechaActualizacion', 'sub_intIdSubCuentaContable', 'prv_intIdProveedor', 'est_intIdEstado'],

			'rules' 		=> [
				'tip_intId' => ['type' => 'int'],
				'tip_dtimFechaCreacion' => ['type' => 'datetime'],
				'tip_dtimFechaActualizacion' => ['type' => 'datetime'],
				'sub_intIdSubCuentaContable' => ['type' => 'int'],
				'prv_intIdProveedor' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_proveedor_informacion_tributaria.est_intIdEstado']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.prv_intId','tbl_proveedor_informacion_tributaria.prv_intIdProveedor']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_proveedor_informacion_tributaria.sub_intIdSubCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_proveedor_informacion_tributaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_proveedor_informacion_tributaria.usu_intIdCreador']
				]
			]
		];
	}	
}


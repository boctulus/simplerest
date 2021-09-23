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

			'nullable'		=> ['tip_intId', 'sub_intIdSubCuentaContable', 'prv_intIdProveedor'],

			'rules' 		=> [

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
					['usu_intIdActualizadors.usu_intId','tbl_proveedor_informacion_tributaria.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_proveedor_informacion_tributaria.usu_intIdCreador']
				]
			]
		];
	}	
}


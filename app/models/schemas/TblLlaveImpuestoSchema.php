<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblLlaveImpuestoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_llave_impuesto',

			'id_name'		=> 'lla_intId',

			'attr_types'	=> [
				'lla_intId' => 'INT',
				'lla_varNombreLLave' => 'STR',
				'lla_dtimFechaCreacion' => 'STR',
				'lla_dtimFechaActualizacion' => 'STR',
				'ret_intIdRetencionCuentacontable' => 'INT',
				'iva_intIdIvaCuentaContable' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['lla_intId'],

			'rules' 		=> [
				'lla_varNombreLLave' => ['max' => 50]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_llave_impuesto.est_intIdEstado']
				],
				'tbl_iva_cuentacontable' => [
					['tbl_iva_cuentacontable.ivc_intId','tbl_llave_impuesto.iva_intIdIvaCuentaContable']
				],
				'tbl_retencion_cuentacontable' => [
					['tbl_retencion_cuentacontable.rec_intId','tbl_llave_impuesto.ret_intIdRetencionCuentacontable']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_llave_impuesto.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_llave_impuesto.usu_intIdCreador']
				]
			]
		];
	}	
}


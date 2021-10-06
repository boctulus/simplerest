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

			'nullable'		=> ['lla_intId', 'lla_dtimFechaCreacion', 'lla_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'rules' 		=> [
				'lla_intId' => ['type' => 'int'],
				'lla_varNombreLLave' => ['type' => 'str', 'max' => 50, 'required' => true],
				'lla_dtimFechaCreacion' => ['type' => 'datetime'],
				'lla_dtimFechaActualizacion' => ['type' => 'datetime'],
				'ret_intIdRetencionCuentacontable' => ['type' => 'int', 'required' => true],
				'iva_intIdIvaCuentaContable' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
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
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_llave_impuesto.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_llave_impuesto.usu_intIdCreador']
				]
			]
		];
	}	
}


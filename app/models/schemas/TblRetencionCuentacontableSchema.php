<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRetencionCuentacontableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_retencion_cuentacontable',

			'id_name'		=> 'rec_intIdCuentaContable',

			'attr_types'	=> [
				'rec_intId' => 'INT',
				'rec_intIdRetencion' => 'INT',
				'rec_intIdCuentaContable' => 'INT',
				'rec_dtimFechaCreacion' => 'STR',
				'rec_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['rec_intId'],

			'rules' 		=> [

			],

			'relationships' => [
				'tbl_retencion' => [
					['tbl_retencion.ret_intId','tbl_retencion_cuentacontable.rec_intIdRetencion']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_retencion_cuentacontable.rec_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_retencion_cuentacontable.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_retencion_cuentacontable.usu_intIdCreador']
				],
				'tbl_llave_impuesto' => [
					['tbl_llave_impuesto.ret_intIdRetencionCuentacontable','tbl_retencion_cuentacontable.rec_intId']
				]
			]
		];
	}	
}


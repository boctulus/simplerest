<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRetencionCuentacontableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_retencion_cuentacontable',

			'id_name'		=> 'rec_intId',

			'attr_types'	=> [
				'rec_intId' => 'INT',
				'rec_intIdRetencion' => 'INT',
				'rec_intIdCuentaContable' => 'INT',
				'rec_dtimFechaCreacion' => 'STR',
				'rec_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['rec_intId', 'rec_dtimFechaCreacion', 'rec_dtimFechaActualizacion'],

			'rules' 		=> [
				'rec_intId' => ['type' => 'int'],
				'rec_intIdRetencion' => ['type' => 'int', 'required' => true],
				'rec_intIdCuentaContable' => ['type' => 'int', 'required' => true],
				'rec_dtimFechaCreacion' => ['type' => 'datetime'],
				'rec_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_retencion' => [
					['tbl_retencion.ret_intId','tbl_retencion_cuentacontable.rec_intIdRetencion']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_retencion_cuentacontable.rec_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_retencion_cuentacontable.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_retencion_cuentacontable.usu_intIdActualizador']
				],
				'tbl_llave_impuesto' => [
					['tbl_llave_impuesto.ret_intIdRetencionCuentacontable','tbl_retencion_cuentacontable.rec_intId']
				]
			]
		];
	}	
}

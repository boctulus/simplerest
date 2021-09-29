<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRetencionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_retencion',

			'id_name'		=> 'ret_intId',

			'attr_types'	=> [
				'ret_intId' => 'INT',
				'ret_varRetencion' => 'STR',
				'ret_intTope' => 'INT',
				'ret_decPorcentaje' => 'STR',
				'ret_bolEstado' => 'INT',
				'ret_varCodigoSiesa' => 'STR',
				'ret_varCuentaArbo' => 'STR',
				'ret_dtimFechaCreacion' => 'STR',
				'ret_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT',
				'sub_intIdCuentaContable' => 'INT'
			],

			'nullable'		=> ['ret_intId', 'sub_intIdCuentaContable'],

			'rules' 		=> [
				'ret_varRetencion' => ['max' => 50],
				'ret_varCodigoSiesa' => ['max' => 10],
				'ret_varCuentaArbo' => ['max' => 50]
			],

			'relationships' => [
				'tbl_retencion_cuentacontable' => [
					['tbl_retencion_cuentacontable.rec_intIdRetencion','tbl_retencion.ret_intId']
				]
			]
		];
	}	
}


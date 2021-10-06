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

			'nullable'		=> ['ret_intId', 'ret_bolEstado', 'ret_dtimFechaCreacion', 'ret_dtimFechaActualizacion', 'sub_intIdCuentaContable'],

			'rules' 		=> [
				'ret_decPorcentaje' => ['type' => 'str', 'required' => true],
				'ret_intId' => ['type' => 'int'],
				'ret_varRetencion' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ret_intTope' => ['type' => 'int', 'required' => true],
				'ret_bolEstado' => ['type' => 'bool'],
				'ret_varCodigoSiesa' => ['type' => 'str', 'max' => 10, 'required' => true],
				'ret_varCuentaArbo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ret_dtimFechaCreacion' => ['type' => 'datetime'],
				'ret_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true],
				'sub_intIdCuentaContable' => ['type' => 'int']
			],

			'relationships' => [
				'tbl_retencion_cuentacontable' => [
					['tbl_retencion_cuentacontable.rec_intIdRetencion','tbl_retencion.ret_intId']
				]
			]
		];
	}	
}


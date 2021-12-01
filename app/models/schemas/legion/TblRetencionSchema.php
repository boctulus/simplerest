<?php

namespace simplerest\models\schemas\legion;

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

			'primary'		=> ['ret_intId'],

			'autoincrement' => 'ret_intId',

			'nullable'		=> ['ret_intId', 'ret_bolEstado', 'ret_dtimFechaCreacion', 'ret_dtimFechaActualizacion', 'sub_intIdCuentaContable'],

			'uniques'		=> ['ret_varRetencion'],

			'rules' 		=> [
				'ret_intId' => ['type' => 'int'],
				'ret_varRetencion' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ret_intTope' => ['type' => 'int', 'required' => true],
				'ret_decPorcentaje' => ['type' => 'decimal(10,2)', 'required' => true],
				'ret_bolEstado' => ['type' => 'bool'],
				'ret_varCodigoSiesa' => ['type' => 'str', 'max' => 10, 'required' => true],
				'ret_varCuentaArbo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ret_dtimFechaCreacion' => ['type' => 'datetime'],
				'ret_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true],
				'sub_intIdCuentaContable' => ['type' => 'int']
			],

			'fks' 			=> [],

			'relationships' => [
				'tbl_retencion_cuentacontable' => [
					['tbl_retencion_cuentacontable.rec_intIdRetencion','tbl_retencion.ret_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_retencion_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion_cuentacontable',
				        1 => 'rec_intIdRetencion',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'ret_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}


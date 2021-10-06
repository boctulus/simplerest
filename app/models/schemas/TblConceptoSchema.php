<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblConceptoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_concepto',

			'id_name'		=> 'cct_intId',

			'attr_types'	=> [
				'cct_intId' => 'INT',
				'cct_varNombre' => 'STR',
				'cct_varDescripcion' => 'STR',
				'cct_dtimFechaCreacion' => 'STR',
				'cct_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['cct_intId', 'cct_dtimFechaCreacion', 'cct_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'cct_intId' => ['type' => 'int'],
				'cct_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'cct_varDescripcion' => ['type' => 'str', 'max' => 250, 'required' => true],
				'cct_dtimFechaCreacion' => ['type' => 'datetime'],
				'cct_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_concepto.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_concepto.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_concepto.usu_intIdCreador']
				]
			]
		];
	}	
}


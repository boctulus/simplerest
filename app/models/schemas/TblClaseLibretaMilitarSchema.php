<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblClaseLibretaMilitarSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_clase_libreta_militar',

			'id_name'		=> 'clm_intId',

			'attr_types'	=> [
				'clm_intId' => 'INT',
				'clm_varNombre' => 'STR',
				'clm_dtimFechaCreacion' => 'STR',
				'clm_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['clm_intId', 'clm_dtimFechaCreacion', 'clm_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'clm_intId' => ['type' => 'int'],
				'clm_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'clm_dtimFechaCreacion' => ['type' => 'datetime'],
				'clm_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_clase_libreta_militar.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_clase_libreta_militar.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_clase_libreta_militar.usu_intIdCreador']
				]
			]
		];
	}	
}


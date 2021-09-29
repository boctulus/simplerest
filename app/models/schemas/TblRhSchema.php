<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRhSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_rh',

			'id_name'		=> 'trh_intId',

			'attr_types'	=> [
				'trh_intId' => 'INT',
				'trh_varCodigo' => 'STR',
				'trh_varDescripcion' => 'STR',
				'trh_dtimFechaCreacion' => 'STR',
				'trh_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['trh_intId', 'est_intIdEstado'],

			'rules' 		=> [
				'trh_varCodigo' => ['max' => 30],
				'trh_varDescripcion' => ['max' => 250]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rh.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rh.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rh.usu_intIdCreador']
				]
			]
		];
	}	
}


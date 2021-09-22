<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPensionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_pension',

			'id_name'		=> 'pen_intId',

			'attr_types'	=> [
				'pen_intId' => 'INT',
				'pen_varCodigo' => 'STR',
				'pen_varNombre' => 'STR',
				'pen_dtimFechaCreacion' => 'STR',
				'pen_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['pen_intId'],

			'rules' 		=> [
				'pen_varCodigo' => ['max' => 100],
				'pen_varNombre' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pension.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_pension.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_pension.usu_intIdCreador']
				]
			]
		];
	}	
}


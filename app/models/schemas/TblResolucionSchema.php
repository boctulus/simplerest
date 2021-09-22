<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblResolucionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_resolucion',

			'id_name'		=> 'res_intId',

			'attr_types'	=> [
				'res_intId' => 'INT',
				'res_varResolucion' => 'STR',
				'res_bolEstado' => 'INT',
				'res_dtimFechaCreacion' => 'STR',
				'res_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['res_intId'],

			'rules' 		=> [
				'res_varResolucion' => ['max' => 100]
			],

			'relationships' => [
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_resolucion.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_resolucion.usu_intIdCreador']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.res_intIdResolucion','tbl_resolucion.res_intId']
				]
			]
		];
	}	
}


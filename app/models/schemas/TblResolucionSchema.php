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

			'nullable'		=> ['res_intId', 'res_varResolucion', 'res_dtimFechaCreacion', 'res_dtimFechaActualizacion'],

			'rules' 		=> [
				'res_intId' => ['type' => 'int'],
				'res_varResolucion' => ['type' => 'str', 'max' => 100],
				'res_bolEstado' => ['type' => 'bool', 'required' => true],
				'res_dtimFechaCreacion' => ['type' => 'datetime'],
				'res_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_resolucion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_resolucion.usu_intIdCreador']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.res_intIdResolucion','tbl_resolucion.res_intId']
				]
			]
		];
	}	
}


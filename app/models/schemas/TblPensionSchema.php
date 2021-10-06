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

			'nullable'		=> ['pen_intId', 'pen_dtimFechaCreacion', 'pen_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'pen_intId' => ['type' => 'int'],
				'pen_varCodigo' => ['type' => 'str', 'max' => 100, 'required' => true],
				'pen_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'pen_dtimFechaCreacion' => ['type' => 'datetime'],
				'pen_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pension.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_pension.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_pension.usu_intIdCreador']
				]
			]
		];
	}	
}


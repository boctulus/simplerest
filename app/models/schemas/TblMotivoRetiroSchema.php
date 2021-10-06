<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblMotivoRetiroSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_motivo_retiro',

			'id_name'		=> 'mtr_intId',

			'attr_types'	=> [
				'mtr_intId' => 'INT',
				'mtr_varNombre' => 'STR',
				'mtr_varDescripcion' => 'STR',
				'mtr_dtimFechaCreacion' => 'STR',
				'mtr_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['mtr_intId', 'mtr_dtimFechaCreacion', 'mtr_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'mtr_intId' => ['type' => 'int'],
				'mtr_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'mtr_varDescripcion' => ['type' => 'str', 'max' => 250, 'required' => true],
				'mtr_dtimFechaCreacion' => ['type' => 'datetime'],
				'mtr_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_motivo_retiro.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_motivo_retiro.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_motivo_retiro.usu_intIdCreador']
				]
			]
		];
	}	
}


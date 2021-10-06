<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEstadoCivilSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_estado_civil',

			'id_name'		=> 'esc_intId',

			'attr_types'	=> [
				'esc_intId' => 'INT',
				'esc_varNombre' => 'STR',
				'esc_dtimFechaCreacion' => 'STR',
				'esc_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['esc_intId', 'esc_dtimFechaCreacion', 'esc_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'esc_intId' => ['type' => 'int'],
				'esc_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'esc_dtimFechaCreacion' => ['type' => 'datetime'],
				'esc_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_estado_civil.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_estado_civil.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_estado_civil.usu_intIdCreador']
				]
			]
		];
	}	
}


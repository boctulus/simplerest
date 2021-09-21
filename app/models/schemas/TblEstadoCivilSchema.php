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

			'nullable'		=> ['esc_intId'],

			'rules' 		=> [
				'esc_varNombre' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_estado_civil.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdCreadors.usu_intId','tbl_estado_civil.usu_intIdCreador'],
					['usu_intIdCreadorss.usu_intId','tbl_estado_civil.usu_intIdActualizador']
				]
			]
		];
	}	
}


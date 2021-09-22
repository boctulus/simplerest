<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEstudiosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_estudios',

			'id_name'		=> 'esd_intId',

			'attr_types'	=> [
				'esd_intId' => 'INT',
				'esd_varNombre' => 'STR',
				'esd_dtimFechaCreacion' => 'STR',
				'esd_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['esd_intId'],

			'rules' 		=> [
				'esd_varNombre' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_estudios.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_estudios.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_estudios.usu_intIdCreador']
				]
			]
		];
	}	
}


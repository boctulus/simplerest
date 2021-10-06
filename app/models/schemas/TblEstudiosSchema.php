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

			'nullable'		=> ['esd_intId', 'esd_dtimFechaCreacion', 'esd_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'esd_intId' => ['type' => 'int'],
				'esd_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'esd_dtimFechaCreacion' => ['type' => 'datetime'],
				'esd_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_estudios.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_estudios.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_estudios.usu_intIdCreador']
				]
			]
		];
	}	
}


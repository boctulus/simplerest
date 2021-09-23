<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEpsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_eps',

			'id_name'		=> 'eps_intId',

			'attr_types'	=> [
				'eps_intId' => 'INT',
				'eps_varCodigo' => 'STR',
				'eps_varNombre' => 'STR',
				'eps_dtimFechaCreacion' => 'STR',
				'eps_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['eps_intId'],

			'rules' 		=> [
				'eps_varCodigo' => ['max' => 100],
				'eps_varNombre' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_eps.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_eps.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_eps.usu_intIdCreador']
				]
			]
		];
	}	
}


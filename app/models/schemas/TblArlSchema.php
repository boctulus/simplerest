<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblArlSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_arl',

			'id_name'		=> 'arl_intId',

			'attr_types'	=> [
				'arl_intId' => 'INT',
				'arl_varCodigo' => 'STR',
				'arl_varNombre' => 'STR',
				'arl_dtimFechaCreacion' => 'STR',
				'arl_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['arl_intId'],

			'rules' 		=> [
				'arl_varCodigo' => ['max' => 100],
				'arl_varNombre' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_arl.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_arl.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_arl.usu_intIdCreador']
				],
				'tbl_empresa' => [
					['tbl_empresa.arl_intIdArl','tbl_arl.arl_intId']
				]
			]
		];
	}	
}


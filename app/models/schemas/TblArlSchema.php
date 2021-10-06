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

			'nullable'		=> ['arl_intId', 'arl_dtimFechaCreacion', 'arl_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'arl_intId' => ['type' => 'int'],
				'arl_varCodigo' => ['type' => 'str', 'max' => 100, 'required' => true],
				'arl_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'arl_dtimFechaCreacion' => ['type' => 'datetime'],
				'arl_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_arl.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_arl.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_arl.usu_intIdCreador']
				],
				'tbl_empresa' => [
					['tbl_empresa.arl_intIdArl','tbl_arl.arl_intId']
				]
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRhSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_rh',

			'id_name'		=> 'trh_intId',

			'attr_types'	=> [
				'trh_intId' => 'INT',
				'trh_varCodigo' => 'STR',
				'trh_varDescripcion' => 'STR',
				'trh_dtimFechaCreacion' => 'STR',
				'trh_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['trh_intId', 'trh_dtimFechaCreacion', 'trh_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'trh_intId' => ['type' => 'int'],
				'trh_varCodigo' => ['type' => 'str', 'max' => 30, 'required' => true],
				'trh_varDescripcion' => ['type' => 'str', 'max' => 250, 'required' => true],
				'trh_dtimFechaCreacion' => ['type' => 'datetime'],
				'trh_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rh.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rh.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rh.usu_intIdCreador']
				]
			]
		];
	}	
}


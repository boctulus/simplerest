<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblUnidadmedidaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_unidadmedida',

			'id_name'		=> 'unm_intId',

			'attr_types'	=> [
				'unm_intId' => 'INT',
				'unm_varUnidadMedida' => 'STR',
				'unm_dtimFechaCreacion' => 'STR',
				'unm_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['unm_intId', 'unm_dtimFechaCreacion', 'unm_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'unm_intId' => ['type' => 'int'],
				'unm_varUnidadMedida' => ['type' => 'str', 'max' => 50, 'required' => true],
				'unm_dtimFechaCreacion' => ['type' => 'datetime'],
				'unm_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_unidadmedida.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_unidadmedida.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_unidadmedida.usu_intIdCreador']
				],
				'tbl_producto' => [
					['tbl_producto.unm_intIdUnidadMedida','tbl_unidadmedida.unm_intId']
				]
			]
		];
	}	
}


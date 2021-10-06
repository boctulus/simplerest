<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCentroCostosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_centro_costos',

			'id_name'		=> 'cco_intId',

			'attr_types'	=> [
				'cco_intId' => 'INT',
				'cco_varCodigo' => 'STR',
				'cco_varCentroCostos' => 'STR',
				'cco_dtimFechaCreacion' => 'STR',
				'cco_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['cco_intId', 'cco_dtimFechaCreacion', 'cco_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'cco_intId' => ['type' => 'int'],
				'cco_varCodigo' => ['type' => 'str', 'max' => 20, 'required' => true],
				'cco_varCentroCostos' => ['type' => 'str', 'max' => 100, 'required' => true],
				'cco_dtimFechaCreacion' => ['type' => 'datetime'],
				'cco_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_centro_costos.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_centro_costos.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_centro_costos.usu_intIdCreador']
				],
				'tbl_factura' => [
					['tbl_factura.cen_intIdCentrocostos','tbl_centro_costos.cco_intId']
				]
			]
		];
	}	
}


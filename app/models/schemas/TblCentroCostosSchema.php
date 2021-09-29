<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCentroCostosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_centro_costos',

			'id_name'		=> 'cco_varCodigo',

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

			'nullable'		=> ['cco_intId'],

			'rules' 		=> [
				'cco_varCodigo' => ['max' => 20],
				'cco_varCentroCostos' => ['max' => 100]
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


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblBodegaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_bodega',

			'id_name'		=> 'bod_intId',

			'attr_types'	=> [
				'bod_intId' => 'INT',
				'bod_varCodigoBodega' => 'STR',
				'bod_varNombreBodega' => 'STR',
				'bod_dtimFechaCreacion' => 'STR',
				'bod_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['bod_intId'],

			'rules' 		=> [
				'bod_varCodigoBodega' => ['max' => 50],
				'bod_varNombreBodega' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_bodega.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_bodega.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_bodega.usu_intIdCreador']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.fde_intIdBodega','tbl_bodega.bod_intId']
				]
			]
		];
	}	
}


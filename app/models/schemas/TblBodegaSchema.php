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

			'nullable'		=> ['bod_intId', 'bod_dtimFechaCreacion', 'bod_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'bod_intId' => ['type' => 'int'],
				'bod_varCodigoBodega' => ['type' => 'str', 'max' => 50, 'required' => true],
				'bod_varNombreBodega' => ['type' => 'str', 'max' => 100, 'required' => true],
				'bod_dtimFechaCreacion' => ['type' => 'datetime'],
				'bod_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_bodega.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_bodega.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_bodega.usu_intIdCreador']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.fde_intIdBodega','tbl_bodega.bod_intId']
				]
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblConsecutivoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_consecutivo',

			'id_name'		=> 'cse_intId',

			'attr_types'	=> [
				'cse_intId' => 'INT',
				'cse_intConsecutivo' => 'INT',
				'cse_varPrefijo' => 'STR',
				'cse_intDesde' => 'INT',
				'cse_intHasta' => 'INT',
				'cse_dateFechaInicial' => 'STR',
				'cse_dateFechaFinal' => 'STR',
				'cse_varVigencia' => 'STR',
				'cse_bolEstado' => 'INT',
				'cse_dtimFechaCreacion' => 'STR',
				'cse_dtimFechaActualizacion' => 'STR',
				'doc_intIdDocumento' => 'INT',
				'res_intIdResolucion' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['cse_intId', 'cse_varPrefijo'],

			'rules' 		=> [
				'cse_varPrefijo' => ['max' => 4],
				'cse_varVigencia' => ['max' => 2]
			],

			'relationships' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_consecutivo.doc_intIdDocumento']
				],
				'tbl_resolucion' => [
					['tbl_resolucion.res_intId','tbl_consecutivo.res_intIdResolucion']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_consecutivo.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_consecutivo.usu_intIdActualizador']
				],
				'tbl_factura' => [
					['tbl_factura.cse_intIdConsecutivo','tbl_consecutivo.cse_intId']
				]
			]
		];
	}	
}


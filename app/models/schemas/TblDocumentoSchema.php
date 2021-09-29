<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblDocumentoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_documento',

			'id_name'		=> 'doc_intId',

			'attr_types'	=> [
				'doc_intId' => 'INT',
				'doc_varDocumento' => 'STR',
				'doc_varDescripcion' => 'STR',
				'doc_bolEstado' => 'INT',
				'doc_dtimFechaCreacion' => 'STR',
				'doc_dtimFechaActualizacion' => 'STR',
				'tra_intIdTransaccion' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['doc_intId'],

			'rules' 		=> [
				'doc_varDocumento' => ['max' => 4],
				'doc_varDescripcion' => ['max' => 150]
			],

			'relationships' => [
				'tbl_transaccion' => [
					['tbl_transaccion.tra_intId','tbl_documento.tra_intIdTransaccion']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_documento.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_documento.usu_intIdCreador']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_factura' => [
					['tbl_factura.doc_intDocumento','tbl_documento.doc_intId']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_preferencias' => [
					['tbl_preferencias.doc_intIdDocumento','tbl_documento.doc_intId']
				]
			]
		];
	}	
}


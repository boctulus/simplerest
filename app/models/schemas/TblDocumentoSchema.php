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

			'nullable'		=> ['doc_intId', 'doc_dtimFechaCreacion', 'doc_dtimFechaActualizacion'],

			'rules' 		=> [
				'doc_intId' => ['type' => 'int'],
				'doc_varDocumento' => ['type' => 'str', 'max' => 4, 'required' => true],
				'doc_varDescripcion' => ['type' => 'str', 'max' => 150, 'required' => true],
				'doc_bolEstado' => ['type' => 'bool', 'required' => true],
				'doc_dtimFechaCreacion' => ['type' => 'datetime'],
				'doc_dtimFechaActualizacion' => ['type' => 'datetime'],
				'tra_intIdTransaccion' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_transaccion' => [
					['tbl_transaccion.tra_intId','tbl_documento.tra_intIdTransaccion']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_documento.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_documento.usu_intIdCreador']
				],
				'tbl_preferencias' => [
					['tbl_preferencias.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_factura' => [
					['tbl_factura.doc_intDocumento','tbl_documento.doc_intId']
				]
			]
		];
	}	
}


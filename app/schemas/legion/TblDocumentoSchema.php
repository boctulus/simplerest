<?php

namespace simplerest\schemas\legion;

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

			'primary'		=> ['doc_intId'],

			'autoincrement' => 'doc_intId',

			'nullable'		=> ['doc_intId', 'doc_dtimFechaCreacion', 'doc_dtimFechaActualizacion'],

			'uniques'		=> ['doc_varDocumento'],

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

			'fks' 			=> ['tra_intIdTransaccion', 'usu_intIdActualizador', 'usu_intIdCreador'],

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
				'tbl_preferencias' => [
					['tbl_preferencias.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_factura' => [
					['tbl_factura.doc_intDocumento','tbl_documento.doc_intId']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_transaccion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_transaccion',
				        1 => 'tra_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'tra_intIdTransaccion',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_consecutivo' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_preferencias' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_preferencias',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_factura' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'doc_intDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_factura_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_factura_detalle',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_transaccion' => [
					['tbl_transaccion.tra_intId','tbl_documento.tra_intIdTransaccion']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_documento.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_documento.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_transaccion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_transaccion',
				        1 => 'tra_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'tra_intIdTransaccion',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


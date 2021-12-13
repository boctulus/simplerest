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
				'doc_lonDescripcion' => 'STR',
				'doc_dtimFechaCreacion' => 'STR',
				'doc_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'tra_intIdTransaccion' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['doc_intId'],

			'autoincrement' => 'doc_intId',

			'nullable'		=> ['doc_intId', 'doc_dtimFechaCreacion', 'doc_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> ['doc_varDocumento'],

			'rules' 		=> [
				'doc_intId' => ['type' => 'int'],
				'doc_varDocumento' => ['type' => 'str', 'max' => 4, 'required' => true],
				'doc_lonDescripcion' => ['type' => 'str', 'required' => true],
				'doc_dtimFechaCreacion' => ['type' => 'datetime'],
				'doc_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'tra_intIdTransaccion' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'tra_intIdTransaccion', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_documento.est_intIdEstado']
				],
				'tbl_transaccion' => [
					['tbl_transaccion.tra_intId','tbl_documento.tra_intIdTransaccion']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_documento.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_documento.usu_intIdCreador']
				],
				'tbl_comprobante_contable' => [
					['tbl_comprobante_contable.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_compras_detalle' => [
					['tbl_compras_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_pedido_detalle' => [
					['tbl_pedido_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_nota_debito_detalle' => [
					['tbl_nota_debito_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_preferencias' => [
					['tbl_preferencias.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_compras' => [
					['tbl_compras.doc_intDocumento','tbl_documento.doc_intId']
				],
				'tbl_contrato' => [
					['tbl_contrato.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_mvto_inventario' => [
					['tbl_mvto_inventario.doc_intDocumento','tbl_documento.doc_intId']
				],
				'tbl_orden_compra' => [
					['tbl_orden_compra.doc_intDocumento','tbl_documento.doc_intId']
				],
				'tbl_pedido' => [
					['tbl_pedido.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_cotizacion_detalle' => [
					['tbl_cotizacion_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_comprobante_contable_detalle' => [
					['tbl_comprobante_contable_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_orden_compra_detalle' => [
					['tbl_orden_compra_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_factura' => [
					['tbl_factura.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_cotizacion' => [
					['tbl_cotizacion.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_mvto_inventario_detalle' => [
					['tbl_mvto_inventario_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_nota_debito' => [
					['tbl_nota_debito.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				],
				'tbl_nota_credito_detalle' => [
					['tbl_nota_credito_detalle.doc_intIdDocumento','tbl_documento.doc_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
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
				  'tbl_comprobante_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_compras_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_compras_detalle',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
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
				  'tbl_pedido_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_debito_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_debito_detalle',
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
				  'tbl_compras' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_compras',
				        1 => 'doc_intDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_contrato' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_mvto_inventario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_mvto_inventario',
				        1 => 'doc_intDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_orden_compra' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_orden_compra',
				        1 => 'doc_intDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_pedido' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pedido',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_cotizacion_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_comprobante_contable_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_credito' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_credito',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_orden_compra_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
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
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_cotizacion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_mvto_inventario_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'doc_intIdDocumento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_debito' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_debito',
				        1 => 'doc_intIdDocumento',
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
				  'tbl_nota_credito_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_credito_detalle',
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
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_documento.est_intIdEstado']
				],
				'tbl_transaccion' => [
					['tbl_transaccion.tra_intId','tbl_documento.tra_intIdTransaccion']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_documento.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_documento.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
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


<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPedidoDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_pedido_detalle',

			'id_name'		=> 'dtp_intId',

			'attr_types'	=> [
				'dtp_intId' => 'INT',
				'dtp_varNroDocumento' => 'STR',
				'dtp_decCantidad' => 'STR',
				'dtp_decPrecioUnitario' => 'STR',
				'dtp_decValor' => 'STR',
				'dtp_decDescuento' => 'STR',
				'dtp_decPorDescuento' => 'STR',
				'dtp_decIva' => 'STR',
				'dtp_decPorcentajeIva' => 'STR',
				'dtp_decRetefuente' => 'STR',
				'dtp_decPorcentajeRetefuente' => 'STR',
				'dtp_decReteIca' => 'STR',
				'dtp_decPorcentajeReteIca' => 'STR',
				'dtp_decReteIva' => 'STR',
				'dtp_decPorcentajeReteiva' => 'STR',
				'dtp_decNeto' => 'STR',
				'dtp_varDescripcionProducto' => 'STR',
				'dtp_datFechaEmision' => 'STR',
				'dtp_datFechaVencimiento' => 'STR',
				'dtp_lonNota' => 'STR',
				'dtp_dtimFechaCreacion' => 'STR',
				'dtp_dtimFechaActualizacion' => 'STR',
				'ecp_intIdPedido' => 'INT',
				'pro_intIdProducto' => 'INT',
				'fde_intIdBodega' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'est_intEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['dtp_intId'],

			'autoincrement' => 'dtp_intId',

			'nullable'		=> ['dtp_intId', 'dtp_varNroDocumento', 'dtp_dtimFechaCreacion', 'dtp_dtimFechaActualizacion', 'doc_intIdDocumento', 'est_intEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'dtp_intId' => ['type' => 'int'],
				'dtp_varNroDocumento' => ['type' => 'str', 'max' => 20],
				'dtp_decCantidad' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decPrecioUnitario' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decValor' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decDescuento' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decPorDescuento' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decIva' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decPorcentajeIva' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decRetefuente' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decPorcentajeRetefuente' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decReteIca' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decPorcentajeReteIca' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decReteIva' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decPorcentajeReteiva' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_decNeto' => ['type' => 'decimal(18,4)', 'required' => true],
				'dtp_varDescripcionProducto' => ['type' => 'str', 'max' => 500, 'required' => true],
				'dtp_datFechaEmision' => ['type' => 'date', 'required' => true],
				'dtp_datFechaVencimiento' => ['type' => 'date', 'required' => true],
				'dtp_lonNota' => ['type' => 'str', 'required' => true],
				'dtp_dtimFechaCreacion' => ['type' => 'datetime'],
				'dtp_dtimFechaActualizacion' => ['type' => 'datetime'],
				'ecp_intIdPedido' => ['type' => 'int', 'required' => true],
				'pro_intIdProducto' => ['type' => 'int', 'required' => true],
				'fde_intIdBodega' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int'],
				'est_intEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['fde_intIdBodega', 'doc_intIdDocumento', 'ecp_intIdPedido', 'est_intEstado', 'pro_intIdProducto', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_pedido_detalle.fde_intIdBodega']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_pedido_detalle.doc_intIdDocumento']
				],
				'tbl_pedido' => [
					['tbl_pedido.ecp_intId','tbl_pedido_detalle.ecp_intIdPedido']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pedido_detalle.est_intEstado']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_pedido_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_pedido_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_pedido_detalle.usu_intIdActualizador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_bodega' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_bodega',
				        1 => 'bod_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'fde_intIdBodega',
				      ),
				    ),
				  ),
				  'tbl_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'ecp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'ecp_intIdPedido',
				      ),
				    ),
				  ),
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
				        0 => 'tbl_pedido_detalle',
				        1 => 'est_intEstado',
				      ),
				    ),
				  ),
				  'tbl_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'pro_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'pro_intIdProducto',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_pedido_detalle.fde_intIdBodega']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_pedido_detalle.doc_intIdDocumento']
				],
				'tbl_pedido' => [
					['tbl_pedido.ecp_intId','tbl_pedido_detalle.ecp_intIdPedido']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pedido_detalle.est_intEstado']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_pedido_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_pedido_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_pedido_detalle.usu_intIdActualizador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_bodega' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_bodega',
				        1 => 'bod_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'fde_intIdBodega',
				      ),
				    ),
				  ),
				  'tbl_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'ecp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'ecp_intIdPedido',
				      ),
				    ),
				  ),
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
				        0 => 'tbl_pedido_detalle',
				        1 => 'est_intEstado',
				      ),
				    ),
				  ),
				  'tbl_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'pro_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'pro_intIdProducto',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblOrdenCompraDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_orden_compra_detalle',

			'id_name'		=> 'ocd_intId',

			'attr_types'	=> [
				'ocd_intId' => 'INT',
				'ocd_dateFecha' => 'STR',
				'ocd_decValor' => 'STR',
				'ocd_bolEstado' => 'INT',
				'ocd_decCantidad' => 'STR',
				'ocd_decCantidadOriginal' => 'STR',
				'ocd_decCantidadPendiente' => 'STR',
				'ocd_decCantidadRecibidad' => 'STR',
				'ocd_decValorTotal' => 'STR',
				'oco_varNumeroDocumento' => 'STR',
				'ocd_varNota' => 'STR',
				'ocd_dtimFechaCreacion' => 'STR',
				'ocd_dtimFechaActualizacion' => 'STR',
				'ocd_dateFechaEntrega' => 'STR',
				'ocd_decIva' => 'STR',
				'ocd_decPorceIVA' => 'STR',
				'ocd_decPorcentajeDescuento' => 'STR',
				'bod_intIdBodega' => 'INT',
				'pro_intIdProducto' => 'INT',
				'oco_intIdordenCompra' => 'INT',
				'per_intIdPersona' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ocd_intId'],

			'autoincrement' => 'ocd_intId',

			'nullable'		=> ['ocd_intId', 'ocd_dateFecha', 'ocd_bolEstado', 'ocd_dtimFechaCreacion', 'ocd_dtimFechaActualizacion', 'bod_intIdBodega', 'pro_intIdProducto'],

			'uniques'		=> [],

			'rules' 		=> [
				'ocd_intId' => ['type' => 'int'],
				'ocd_dateFecha' => ['type' => 'datetime'],
				'ocd_decValor' => ['type' => 'decimal(18,2)', 'required' => true],
				'ocd_bolEstado' => ['type' => 'bool'],
				'ocd_decCantidad' => ['type' => 'decimal(18,2)', 'required' => true],
				'ocd_decCantidadOriginal' => ['type' => 'decimal(18,2)', 'required' => true],
				'ocd_decCantidadPendiente' => ['type' => 'decimal(18,2)', 'required' => true],
				'ocd_decCantidadRecibidad' => ['type' => 'decimal(18,2)', 'required' => true],
				'ocd_decValorTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'oco_varNumeroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'ocd_varNota' => ['type' => 'str', 'max' => 250, 'required' => true],
				'ocd_dtimFechaCreacion' => ['type' => 'datetime'],
				'ocd_dtimFechaActualizacion' => ['type' => 'datetime'],
				'ocd_dateFechaEntrega' => ['type' => 'date', 'required' => true],
				'ocd_decIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'ocd_decPorceIVA' => ['type' => 'decimal(10,2)', 'required' => true],
				'ocd_decPorcentajeDescuento' => ['type' => 'decimal(10,2)', 'required' => true],
				'bod_intIdBodega' => ['type' => 'int'],
				'pro_intIdProducto' => ['type' => 'int'],
				'oco_intIdordenCompra' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['bod_intIdBodega', 'doc_intIdDocumento', 'oco_intIdordenCompra', 'per_intIdPersona', 'pro_intIdProducto', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_orden_compra_detalle.bod_intIdBodega']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_orden_compra_detalle.doc_intIdDocumento']
				],
				'tbl_orden_compra' => [
					['tbl_orden_compra.oco_intId','tbl_orden_compra_detalle.oco_intIdordenCompra']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_orden_compra_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_orden_compra_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_orden_compra_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_orden_compra_detalle.usu_intIdCreador']
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
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'bod_intIdBodega',
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
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'oco_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'oco_intIdordenCompra',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'per_intIdPersona',
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
				        0 => 'tbl_orden_compra_detalle',
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
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
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
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_orden_compra_detalle.bod_intIdBodega']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_orden_compra_detalle.doc_intIdDocumento']
				],
				'tbl_orden_compra' => [
					['tbl_orden_compra.oco_intId','tbl_orden_compra_detalle.oco_intIdordenCompra']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_orden_compra_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_orden_compra_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_orden_compra_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_orden_compra_detalle.usu_intIdCreador']
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
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'bod_intIdBodega',
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
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'oco_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'oco_intIdordenCompra',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'per_intIdPersona',
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
				        0 => 'tbl_orden_compra_detalle',
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
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
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
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


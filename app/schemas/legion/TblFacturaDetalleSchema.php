<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblFacturaDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_factura_detalle',

			'id_name'		=> 'fde_intId',

			'attr_types'	=> [
				'fde_intId' => 'INT',
				'fde_dateFecha' => 'STR',
				'fde_decValor' => 'STR',
				'fde_bolEstado' => 'INT',
				'fde_decCantidad' => 'STR',
				'fde_decValorTotal' => 'STR',
				'fde_decPorcentajeIva' => 'STR',
				'fde_decValorIva' => 'STR',
				'fde_decPorcentajeDescuento' => 'STR',
				'fde_decValorDescuento' => 'STR',
				'fde_decPorcentajeRetefuente' => 'STR',
				'fde_decValorRetefuente' => 'STR',
				'fde_decPorcentajeReteIva' => 'STR',
				'fde_decValorReteIva' => 'STR',
				'fde_decPorcentajeReteIca' => 'STR',
				'fde_decValorReteIca' => 'STR',
				'fde_dtimFechaCreacion' => 'STR',
				'fde_dtimFechaActualizacion' => 'STR',
				'fac_intNroDocumento' => 'STR',
				'fde_varDescripcion' => 'STR',
				'fac_intIdFactura' => 'INT',
				'pro_intIdProducto' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'fde_intIdBodega' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['fde_intId'],

			'autoincrement' => 'fde_intId',

			'nullable'		=> ['fde_intId', 'fde_bolEstado', 'fde_dtimFechaCreacion', 'fde_dtimFechaActualizacion', 'fde_intIdBodega'],

			'uniques'		=> [],

			'rules' 		=> [
				'fde_intId' => ['type' => 'int'],
				'fde_dateFecha' => ['type' => 'date', 'required' => true],
				'fde_decValor' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_bolEstado' => ['type' => 'bool'],
				'fde_decCantidad' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decValorTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decPorcentajeIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decValorIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decPorcentajeDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decValorDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decPorcentajeRetefuente' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decValorRetefuente' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decPorcentajeReteIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decValorReteIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decPorcentajeReteIca' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_decValorReteIca' => ['type' => 'decimal(18,2)', 'required' => true],
				'fde_dtimFechaCreacion' => ['type' => 'datetime'],
				'fde_dtimFechaActualizacion' => ['type' => 'datetime'],
				'fac_intNroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'fde_varDescripcion' => ['type' => 'str', 'required' => true],
				'fac_intIdFactura' => ['type' => 'int', 'required' => true],
				'pro_intIdProducto' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'fde_intIdBodega' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['fde_intIdBodega', 'doc_intIdDocumento', 'fac_intIdFactura', 'pro_intIdProducto', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_factura_detalle.fde_intIdBodega']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_factura_detalle.doc_intIdDocumento']
				],
				'tbl_factura' => [
					['tbl_factura.fac_intId','tbl_factura_detalle.fac_intIdFactura']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_factura_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_factura_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_factura_detalle.usu_intIdCreador']
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
				        0 => 'tbl_factura_detalle',
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
				        0 => 'tbl_factura_detalle',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'fac_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_factura_detalle',
				        1 => 'fac_intIdFactura',
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
				        0 => 'tbl_factura_detalle',
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
				        0 => 'tbl_factura_detalle',
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
				        0 => 'tbl_factura_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_factura_detalle.fde_intIdBodega']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_factura_detalle.doc_intIdDocumento']
				],
				'tbl_factura' => [
					['tbl_factura.fac_intId','tbl_factura_detalle.fac_intIdFactura']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_factura_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_factura_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_factura_detalle.usu_intIdCreador']
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
				        0 => 'tbl_factura_detalle',
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
				        0 => 'tbl_factura_detalle',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'fac_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_factura_detalle',
				        1 => 'fac_intIdFactura',
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
				        0 => 'tbl_factura_detalle',
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
				        0 => 'tbl_factura_detalle',
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
				        0 => 'tbl_factura_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


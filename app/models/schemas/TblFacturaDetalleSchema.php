<?php

namespace simplerest\models\schemas;

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

			'nullable'		=> ['fde_intId', 'fde_bolEstado', 'fde_dtimFechaCreacion', 'fde_dtimFechaActualizacion'],

			'rules' 		=> [
				'fde_decValor' => ['type' => 'str', 'required' => true],
				'fde_decCantidad' => ['type' => 'str', 'required' => true],
				'fde_decValorTotal' => ['type' => 'str', 'required' => true],
				'fde_decPorcentajeIva' => ['type' => 'str', 'required' => true],
				'fde_decValorIva' => ['type' => 'str', 'required' => true],
				'fde_decPorcentajeDescuento' => ['type' => 'str', 'required' => true],
				'fde_decValorDescuento' => ['type' => 'str', 'required' => true],
				'fde_decPorcentajeRetefuente' => ['type' => 'str', 'required' => true],
				'fde_decValorRetefuente' => ['type' => 'str', 'required' => true],
				'fde_decPorcentajeReteIva' => ['type' => 'str', 'required' => true],
				'fde_decValorReteIva' => ['type' => 'str', 'required' => true],
				'fde_decPorcentajeReteIca' => ['type' => 'str', 'required' => true],
				'fde_decValorReteIca' => ['type' => 'str', 'required' => true],
				'fde_intId' => ['type' => 'int'],
				'fde_dateFecha' => ['type' => 'date', 'required' => true],
				'fde_bolEstado' => ['type' => 'bool'],
				'fde_dtimFechaCreacion' => ['type' => 'datetime'],
				'fde_dtimFechaActualizacion' => ['type' => 'datetime'],
				'fac_intNroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'fde_varDescripcion' => ['type' => 'str', 'required' => true],
				'fac_intIdFactura' => ['type' => 'int', 'required' => true],
				'pro_intIdProducto' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'fde_intIdBodega' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

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
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_factura_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_factura_detalle.usu_intIdActualizador']
				]
			]
		];
	}	
}


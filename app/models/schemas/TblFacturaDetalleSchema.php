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

			'nullable'		=> ['fde_intId'],

			'rules' 		=> [
				'fac_intNroDocumento' => ['max' => 20]
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
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_factura_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_factura_detalle.usu_intIdCreador']
				]
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblProductoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_producto',

			'id_name'		=> 'pro_intId',

			'attr_types'	=> [
				'pro_intId' => 'INT',
				'pro_varCodigoProducto' => 'STR',
				'pro_varNombreProducto' => 'STR',
				'pro_intCodigoBarras' => 'INT',
				'pro_intCostoCompra' => 'INT',
				'pro_intPrecioVenta' => 'INT',
				'pro_intStockMinimo' => 'INT',
				'pro_intSaldo' => 'INT',
				'pro_intStockMaximo' => 'INT',
				'pro_dtimFechaCreacion' => 'STR',
				'pro_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'sub_intIdCuentaContableCompra' => 'INT',
				'sub_intIdCuentaContableVenta' => 'INT',
				'mon_intIdMoneda' => 'INT',
				'iva_intIdIva' => 'INT',
				'unm_intIdUnidadMedida' => 'INT',
				'cap_intIdCategoriaProducto' => 'INT',
				'grp_intIdGrupoProducto' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['pro_intId', 'pro_dtimFechaCreacion', 'pro_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'pro_intId' => ['type' => 'int'],
				'pro_varCodigoProducto' => ['type' => 'str', 'max' => 50, 'required' => true],
				'pro_varNombreProducto' => ['type' => 'str', 'max' => 50, 'required' => true],
				'pro_intCodigoBarras' => ['type' => 'int', 'required' => true],
				'pro_intCostoCompra' => ['type' => 'int', 'required' => true],
				'pro_intPrecioVenta' => ['type' => 'int', 'required' => true],
				'pro_intStockMinimo' => ['type' => 'int', 'required' => true],
				'pro_intSaldo' => ['type' => 'int', 'required' => true],
				'pro_intStockMaximo' => ['type' => 'int', 'required' => true],
				'pro_dtimFechaCreacion' => ['type' => 'datetime'],
				'pro_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'sub_intIdCuentaContableCompra' => ['type' => 'int', 'required' => true],
				'sub_intIdCuentaContableVenta' => ['type' => 'int', 'required' => true],
				'mon_intIdMoneda' => ['type' => 'int', 'required' => true],
				'iva_intIdIva' => ['type' => 'int', 'required' => true],
				'unm_intIdUnidadMedida' => ['type' => 'int', 'required' => true],
				'cap_intIdCategoriaProducto' => ['type' => 'int', 'required' => true],
				'grp_intIdGrupoProducto' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_categoria_producto' => [
					['tbl_categoria_producto.cap_intId','tbl_producto.cap_intIdCategoriaProducto']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_producto.est_intIdEstado']
				],
				'tbl_grupo_producto' => [
					['tbl_grupo_producto.grp_intId','tbl_producto.grp_intIdGrupoProducto']
				],
				'tbl_iva' => [
					['tbl_iva.iva_intId','tbl_producto.iva_intIdIva']
				],
				'tbl_moneda' => [
					['tbl_moneda.mon_intId','tbl_producto.mon_intIdMoneda']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_producto.sub_intIdCuentaContableCompra'],
					['tbl_sub_cuenta_contable.sub_intId','tbl_producto.sub_intIdCuentaContableVenta']
				],
				'tbl_unidadmedida' => [
					['tbl_unidadmedida.unm_intId','tbl_producto.unm_intIdUnidadMedida']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_producto.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_producto.usu_intIdCreador']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.pro_intIdProducto','tbl_producto.pro_intId']
				]
			]
		];
	}	
}


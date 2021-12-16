<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCotizacionDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cotizacion_detalle',

			'id_name'		=> 'cde_intId',

			'attr_types'	=> [
				'cde_intId' => 'INT',
				'cde_fecha' => 'STR',
				'cde_decValor' => 'STR',
				'cde_bolEstado' => 'INT',
				'cde_decCantidad' => 'STR',
				'cde_decValorTotal' => 'STR',
				'cde_decPorcentajeIva' => 'STR',
				'cde_decValorIva' => 'STR',
				'cde_decPorcentajeDescuento' => 'STR',
				'cde_decValorDescuento' => 'STR',
				'cde_dtimFechaCreacion' => 'STR',
				'cde_dtimFechaActualizacion' => 'STR',
				'cot_intNroDocumento' => 'STR',
				'cde_varDescripcion' => 'STR',
				'cot_intIdCotizacion' => 'INT',
				'pro_intIdProducto' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cde_intId'],

			'autoincrement' => 'cde_intId',

			'nullable'		=> ['cde_intId', 'cde_bolEstado', 'cde_dtimFechaCreacion', 'cde_dtimFechaActualizacion', 'cot_intNroDocumento', 'pro_intIdProducto'],

			'uniques'		=> [],

			'rules' 		=> [
				'cde_intId' => ['type' => 'int'],
				'cde_fecha' => ['type' => 'date', 'required' => true],
				'cde_decValor' => ['type' => 'decimal(18,2)', 'required' => true],
				'cde_bolEstado' => ['type' => 'bool'],
				'cde_decCantidad' => ['type' => 'decimal(18,2)', 'required' => true],
				'cde_decValorTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'cde_decPorcentajeIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'cde_decValorIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'cde_decPorcentajeDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'cde_decValorDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'cde_dtimFechaCreacion' => ['type' => 'datetime'],
				'cde_dtimFechaActualizacion' => ['type' => 'datetime'],
				'cot_intNroDocumento' => ['type' => 'str', 'max' => 20],
				'cde_varDescripcion' => ['type' => 'str', 'required' => true],
				'cot_intIdCotizacion' => ['type' => 'int', 'required' => true],
				'pro_intIdProducto' => ['type' => 'int'],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['cot_intIdCotizacion', 'doc_intIdDocumento', 'pro_intIdProducto', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_cotizacion' => [
					['tbl_cotizacion.cot_intId','tbl_cotizacion_detalle.cot_intIdCotizacion']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_cotizacion_detalle.doc_intIdDocumento']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_cotizacion_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cotizacion_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cotizacion_detalle.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_cotizacion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion',
				        1 => 'cot_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'cot_intIdCotizacion',
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
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_cotizacion_detalle',
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
				        0 => 'tbl_cotizacion_detalle',
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
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_cotizacion' => [
					['tbl_cotizacion.cot_intId','tbl_cotizacion_detalle.cot_intIdCotizacion']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_cotizacion_detalle.doc_intIdDocumento']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_cotizacion_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cotizacion_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cotizacion_detalle.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_cotizacion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion',
				        1 => 'cot_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'cot_intIdCotizacion',
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
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_cotizacion_detalle',
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
				        0 => 'tbl_cotizacion_detalle',
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
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


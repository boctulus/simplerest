<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblComprasDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_compras_detalle',

			'id_name'		=> 'cmd_intId',

			'attr_types'	=> [
				'cmd_intId' => 'INT',
				'cmd_varNroDocumento' => 'STR',
				'cmd_datFecha' => 'STR',
				'cmd_decCantidad' => 'STR',
				'cmd_decValor' => 'STR',
				'cmd_decIva' => 'STR',
				'cmd_decPorceIva' => 'STR',
				'cmd_decPorcentajeDescuento' => 'STR',
				'cmd_decValorTotal' => 'STR',
				'cmd_lonNota' => 'STR',
				'oco_varNumeroOC' => 'STR',
				'oco_intIdOC' => 'INT',
				'doc_intDocumentoOC' => 'INT',
				'cmd_dtimFechaCreacion' => 'STR',
				'cmd_dtimFechaActualizacion' => 'STR',
				'com_intIdCompras' => 'INT',
				'pro_intIdProducto' => 'INT',
				'per_intIdPersona' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'bod_intIdBodega' => 'INT',
				'cen_intIdCentrocostos' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cmd_intId'],

			'autoincrement' => 'cmd_intId',

			'nullable'		=> ['cmd_intId', 'cmd_dtimFechaCreacion', 'cmd_dtimFechaActualizacion', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'cmd_intId' => ['type' => 'int'],
				'cmd_varNroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'cmd_datFecha' => ['type' => 'date', 'required' => true],
				'cmd_decCantidad' => ['type' => 'decimal(18,2)', 'required' => true],
				'cmd_decValor' => ['type' => 'decimal(18,4)', 'required' => true],
				'cmd_decIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'cmd_decPorceIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'cmd_decPorcentajeDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'cmd_decValorTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'cmd_lonNota' => ['type' => 'str', 'required' => true],
				'oco_varNumeroOC' => ['type' => 'str', 'max' => 20, 'required' => true],
				'oco_intIdOC' => ['type' => 'int', 'required' => true],
				'doc_intDocumentoOC' => ['type' => 'int', 'required' => true],
				'cmd_dtimFechaCreacion' => ['type' => 'datetime'],
				'cmd_dtimFechaActualizacion' => ['type' => 'datetime'],
				'com_intIdCompras' => ['type' => 'int', 'required' => true],
				'pro_intIdProducto' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'bod_intIdBodega' => ['type' => 'int', 'required' => true],
				'cen_intIdCentrocostos' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['bod_intIdBodega', 'cen_intIdCentrocostos', 'com_intIdCompras', 'doc_intIdDocumento', 'per_intIdPersona', 'pro_intIdProducto', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_compras_detalle.bod_intIdBodega']
				],
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_compras_detalle.cen_intIdCentrocostos']
				],
				'tbl_compras' => [
					['tbl_compras.com_intId','tbl_compras_detalle.com_intIdCompras']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_compras_detalle.doc_intIdDocumento']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_compras_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_compras_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_compras_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_compras_detalle.usu_intIdActualizador']
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
				        0 => 'tbl_compras_detalle',
				        1 => 'bod_intIdBodega',
				      ),
				    ),
				  ),
				  'tbl_centro_costos' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_compras_detalle',
				        1 => 'cen_intIdCentrocostos',
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
				        1 => 'com_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_compras_detalle',
				        1 => 'com_intIdCompras',
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
				        0 => 'tbl_compras_detalle',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_compras_detalle',
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
				        0 => 'tbl_compras_detalle',
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
				        0 => 'tbl_compras_detalle',
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
				        0 => 'tbl_compras_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_compras_detalle.bod_intIdBodega']
				],
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_compras_detalle.cen_intIdCentrocostos']
				],
				'tbl_compras' => [
					['tbl_compras.com_intId','tbl_compras_detalle.com_intIdCompras']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_compras_detalle.doc_intIdDocumento']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_compras_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_compras_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_compras_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_compras_detalle.usu_intIdActualizador']
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
				        0 => 'tbl_compras_detalle',
				        1 => 'bod_intIdBodega',
				      ),
				    ),
				  ),
				  'tbl_centro_costos' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_compras_detalle',
				        1 => 'cen_intIdCentrocostos',
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
				        1 => 'com_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_compras_detalle',
				        1 => 'com_intIdCompras',
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
				        0 => 'tbl_compras_detalle',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_compras_detalle',
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
				        0 => 'tbl_compras_detalle',
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
				        0 => 'tbl_compras_detalle',
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
				        0 => 'tbl_compras_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


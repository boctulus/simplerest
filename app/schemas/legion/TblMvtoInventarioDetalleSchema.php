<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblMvtoInventarioDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_mvto_inventario_detalle',

			'id_name'		=> 'mvd_intId',

			'attr_types'	=> [
				'mvd_intId' => 'INT',
				'mvd_varNumeroDocumento' => 'STR',
				'mvd_varDescripcion' => 'STR',
				'mvd_dateFecha' => 'STR',
				'mvd_decCantidad' => 'STR',
				'mvd_decValor' => 'STR',
				'mvd_decIva' => 'STR',
				'mvd_decPorceIVA' => 'STR',
				'mvd_decPorcentajeDescuento' => 'STR',
				'mvd_decValorTotal' => 'STR',
				'mvd_varNota' => 'STR',
				'mvd_dtimFechaCreacion' => 'STR',
				'mvd_dtimFechaActualizacion' => 'STR',
				'bod_intIdBodega' => 'INT',
				'pro_intIdProducto' => 'INT',
				'est_intIdEstado' => 'INT',
				'mvi_intIdMvtoInventario' => 'INT',
				'per_intIdPersona' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['mvd_intId'],

			'autoincrement' => 'mvd_intId',

			'nullable'		=> ['mvd_intId', 'mvd_varNumeroDocumento', 'mvd_dtimFechaCreacion', 'mvd_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'mvd_intId' => ['type' => 'int'],
				'mvd_varNumeroDocumento' => ['type' => 'str', 'max' => 20],
				'mvd_varDescripcion' => ['type' => 'str', 'max' => 100, 'required' => true],
				'mvd_dateFecha' => ['type' => 'date', 'required' => true],
				'mvd_decCantidad' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvd_decValor' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvd_decIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvd_decPorceIVA' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvd_decPorcentajeDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvd_decValorTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvd_varNota' => ['type' => 'str', 'max' => 255, 'required' => true],
				'mvd_dtimFechaCreacion' => ['type' => 'datetime'],
				'mvd_dtimFechaActualizacion' => ['type' => 'datetime'],
				'bod_intIdBodega' => ['type' => 'int', 'required' => true],
				'pro_intIdProducto' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'mvi_intIdMvtoInventario' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['bod_intIdBodega', 'doc_intIdDocumento', 'est_intIdEstado', 'mvi_intIdMvtoInventario', 'per_intIdPersona', 'pro_intIdProducto', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_mvto_inventario_detalle.bod_intIdBodega']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_mvto_inventario_detalle.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_mvto_inventario_detalle.est_intIdEstado']
				],
				'tbl_mvto_inventario' => [
					['tbl_mvto_inventario.mvi_intId','tbl_mvto_inventario_detalle.mvi_intIdMvtoInventario']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_mvto_inventario_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_mvto_inventario_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_mvto_inventario_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_mvto_inventario_detalle.usu_intIdCreador']
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
				        0 => 'tbl_mvto_inventario_detalle',
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
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'est_intIdEstado',
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
				        1 => 'mvi_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'mvi_intIdMvtoInventario',
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
				        0 => 'tbl_mvto_inventario_detalle',
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
				        0 => 'tbl_mvto_inventario_detalle',
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
				        0 => 'tbl_mvto_inventario_detalle',
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
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_bodega' => [
					['tbl_bodega.bod_intId','tbl_mvto_inventario_detalle.bod_intIdBodega']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_mvto_inventario_detalle.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_mvto_inventario_detalle.est_intIdEstado']
				],
				'tbl_mvto_inventario' => [
					['tbl_mvto_inventario.mvi_intId','tbl_mvto_inventario_detalle.mvi_intIdMvtoInventario']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_mvto_inventario_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_mvto_inventario_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_mvto_inventario_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_mvto_inventario_detalle.usu_intIdCreador']
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
				        0 => 'tbl_mvto_inventario_detalle',
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
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'est_intIdEstado',
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
				        1 => 'mvi_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'mvi_intIdMvtoInventario',
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
				        0 => 'tbl_mvto_inventario_detalle',
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
				        0 => 'tbl_mvto_inventario_detalle',
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
				        0 => 'tbl_mvto_inventario_detalle',
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
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


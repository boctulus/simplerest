<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblBodegaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_bodega',

			'id_name'		=> 'bod_intId',

			'attr_types'	=> [
				'bod_intId' => 'INT',
				'bod_varCodigo' => 'STR',
				'bod_varNombre' => 'STR',
				'bod_lonDescripcion' => 'STR',
				'bod_dtimFechaCreacion' => 'STR',
				'bod_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['bod_intId'],

			'autoincrement' => 'bod_intId',

			'nullable'		=> ['bod_intId', 'bod_dtimFechaCreacion', 'bod_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'bod_intId' => ['type' => 'int'],
				'bod_varCodigo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'bod_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'bod_lonDescripcion' => ['type' => 'str', 'required' => true],
				'bod_dtimFechaCreacion' => ['type' => 'datetime'],
				'bod_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_bodega.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_bodega.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_bodega.usu_intIdCreador']
				],
				'tbl_pedido_detalle' => [
					['tbl_pedido_detalle.fde_intIdBodega','tbl_bodega.bod_intId']
				],
				'tbl_orden_compra_detalle' => [
					['tbl_orden_compra_detalle.bod_intIdBodega','tbl_bodega.bod_intId']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.fde_intIdBodega','tbl_bodega.bod_intId']
				],
				'tbl_mvto_inventario_detalle' => [
					['tbl_mvto_inventario_detalle.bod_intIdBodega','tbl_bodega.bod_intId']
				],
				'tbl_compras_detalle' => [
					['tbl_compras_detalle.bod_intIdBodega','tbl_bodega.bod_intId']
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
				        0 => 'tbl_bodega',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_bodega',
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
				        0 => 'tbl_bodega',
				        1 => 'usu_intIdCreador',
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
				        1 => 'fde_intIdBodega',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_bodega',
				        1 => 'bod_intId',
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
				        1 => 'bod_intIdBodega',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_bodega',
				        1 => 'bod_intId',
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
				        1 => 'fde_intIdBodega',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_bodega',
				        1 => 'bod_intId',
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
				        1 => 'bod_intIdBodega',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_bodega',
				        1 => 'bod_intId',
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
				        1 => 'bod_intIdBodega',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_bodega',
				        1 => 'bod_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_bodega.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_bodega.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_bodega.usu_intIdCreador']
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
				        0 => 'tbl_bodega',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_bodega',
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
				        0 => 'tbl_bodega',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


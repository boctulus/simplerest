<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCentroCostosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_centro_costos',

			'id_name'		=> 'cco_intId',

			'attr_types'	=> [
				'cco_intId' => 'INT',
				'cco_varCodigo' => 'STR',
				'cco_varCentroCostos' => 'STR',
				'cco_lonDescripcion' => 'STR',
				'cco_dtimFechaCreacion' => 'STR',
				'cco_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cco_intId'],

			'autoincrement' => 'cco_intId',

			'nullable'		=> ['cco_intId', 'cco_varCodigo', 'cco_lonDescripcion', 'cco_dtimFechaCreacion', 'cco_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> ['cco_varCodigo'],

			'rules' 		=> [
				'cco_intId' => ['type' => 'int'],
				'cco_varCodigo' => ['type' => 'str', 'max' => 100],
				'cco_varCentroCostos' => ['type' => 'str', 'max' => 100, 'required' => true],
				'cco_lonDescripcion' => ['type' => 'str'],
				'cco_dtimFechaCreacion' => ['type' => 'datetime'],
				'cco_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_centro_costos.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_centro_costos.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_centro_costos.usu_intIdCreador']
				],
				'tbl_factura' => [
					['tbl_factura.cen_intIdCentrocostos','tbl_centro_costos.cco_intId']
				],
				'tbl_orden_compra' => [
					['tbl_orden_compra.cen_intIdCentrocostos','tbl_centro_costos.cco_intId']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.cen_intIdCentrocostos','tbl_centro_costos.cco_intId']
				],
				'tbl_nota_debito' => [
					['tbl_nota_debito.cen_intIdCentrocostos','tbl_centro_costos.cco_intId']
				],
				'tbl_pedido' => [
					['tbl_pedido.cen_intIdCentrocostos','tbl_centro_costos.cco_intId']
				],
				'tbl_compras_detalle' => [
					['tbl_compras_detalle.cen_intIdCentrocostos','tbl_centro_costos.cco_intId']
				],
				'tbl_contrato' => [
					['tbl_contrato.cco_intIdCentroCostos','tbl_centro_costos.cco_intId']
				],
				'tbl_mvto_inventario' => [
					['tbl_mvto_inventario.cen_intIdCentrocostos','tbl_centro_costos.cco_intId']
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
				        0 => 'tbl_centro_costos',
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
				        0 => 'tbl_centro_costos',
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
				        0 => 'tbl_centro_costos',
				        1 => 'usu_intIdCreador',
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
				        1 => 'cen_intIdCentrocostos',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
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
				        1 => 'cen_intIdCentrocostos',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_credito' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_credito',
				        1 => 'cen_intIdCentrocostos',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_debito' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_debito',
				        1 => 'cen_intIdCentrocostos',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
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
				        1 => 'cen_intIdCentrocostos',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
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
				        1 => 'cen_intIdCentrocostos',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				    ),
				  ),
				  'tbl_contrato' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'cco_intIdCentroCostos',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
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
				        1 => 'cen_intIdCentrocostos',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_centro_costos.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_centro_costos.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_centro_costos.usu_intIdCreador']
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
				        0 => 'tbl_centro_costos',
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
				        0 => 'tbl_centro_costos',
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
				        0 => 'tbl_centro_costos',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


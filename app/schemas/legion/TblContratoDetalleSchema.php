<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblContratoDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_contrato_detalle',

			'id_name'		=> 'cde_intId',

			'attr_types'	=> [
				'cde_intId' => 'INT',
				'cde_decValor' => 'STR',
				'cde_datFechaInicial' => 'STR',
				'cde_datFechaFinal' => 'STR',
				'cde_dtimFechaCreacion' => 'STR',
				'cde_dtimFechaActualizacion' => 'STR',
				'ctr_varNumeroContrato' => 'STR',
				'est_intIdEstado' => 'INT',
				'pro_intIdProducto' => 'INT',
				'ctr_intIdContrato' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cde_intId'],

			'autoincrement' => 'cde_intId',

			'nullable'		=> ['cde_intId', 'cde_dtimFechaCreacion', 'cde_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'cde_intId' => ['type' => 'int'],
				'cde_decValor' => ['type' => 'decimal(18,2)', 'required' => true],
				'cde_datFechaInicial' => ['type' => 'date', 'required' => true],
				'cde_datFechaFinal' => ['type' => 'date', 'required' => true],
				'cde_dtimFechaCreacion' => ['type' => 'datetime'],
				'cde_dtimFechaActualizacion' => ['type' => 'datetime'],
				'ctr_varNumeroContrato' => ['type' => 'str', 'max' => 50, 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'pro_intIdProducto' => ['type' => 'int', 'required' => true],
				'ctr_intIdContrato' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['ctr_intIdContrato', 'est_intIdEstado', 'pro_intIdProducto', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_contrato' => [
					['tbl_contrato.ctr_intId','tbl_contrato_detalle.ctr_intIdContrato']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_contrato_detalle.est_intIdEstado']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_contrato_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_contrato_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_contrato_detalle.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_contrato' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'ctr_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato_detalle',
				        1 => 'ctr_intIdContrato',
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
				        0 => 'tbl_contrato_detalle',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_contrato_detalle',
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
				        0 => 'tbl_contrato_detalle',
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
				        0 => 'tbl_contrato_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_contrato' => [
					['tbl_contrato.ctr_intId','tbl_contrato_detalle.ctr_intIdContrato']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_contrato_detalle.est_intIdEstado']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_contrato_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_contrato_detalle.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_contrato_detalle.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_contrato' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'ctr_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_contrato_detalle',
				        1 => 'ctr_intIdContrato',
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
				        0 => 'tbl_contrato_detalle',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_contrato_detalle',
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
				        0 => 'tbl_contrato_detalle',
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
				        0 => 'tbl_contrato_detalle',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


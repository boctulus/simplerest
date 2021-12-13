<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblDiasPagoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_dias_pago',

			'id_name'		=> 'dpa_intId',

			'attr_types'	=> [
				'dpa_intId' => 'INT',
				'dpa_intDiasPago' => 'INT',
				'dpa_dtimFechaCreacion' => 'STR',
				'dpa_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['dpa_intId'],

			'autoincrement' => 'dpa_intId',

			'nullable'		=> ['dpa_intId', 'dpa_dtimFechaCreacion', 'dpa_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'dpa_intId' => ['type' => 'int'],
				'dpa_intDiasPago' => ['type' => 'int', 'required' => true],
				'dpa_dtimFechaCreacion' => ['type' => 'datetime'],
				'dpa_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_dias_pago.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_dias_pago.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_dias_pago.usu_intIdCreador']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.dpa_intIdDiasPago','tbl_dias_pago.dpa_intId']
				],
				'tbl_cliente' => [
					['tbl_cliente.dpa_intIdDiasPago','tbl_dias_pago.dpa_intId']
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
				        0 => 'tbl_dias_pago',
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
				        0 => 'tbl_dias_pago',
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
				        0 => 'tbl_dias_pago',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_proveedor' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_proveedor',
				        1 => 'dpa_intIdDiasPago',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_dias_pago',
				        1 => 'dpa_intId',
				      ),
				    ),
				  ),
				  'tbl_cliente' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'dpa_intIdDiasPago',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_dias_pago',
				        1 => 'dpa_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_dias_pago.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_dias_pago.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_dias_pago.usu_intIdCreador']
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
				        0 => 'tbl_dias_pago',
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
				        0 => 'tbl_dias_pago',
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
				        0 => 'tbl_dias_pago',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


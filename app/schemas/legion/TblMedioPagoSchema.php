<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblMedioPagoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_medio_pago',

			'id_name'		=> 'tmp_intId',

			'attr_types'	=> [
				'tmp_intId' => 'INT',
				'tmp_varCodigo' => 'STR',
				'tmp_varNombre' => 'STR',
				'tmp_lonDescripcion' => 'STR',
				'tmp_varCodigoDian' => 'STR',
				'tmp_dtimFechaCreacion' => 'STR',
				'tmp_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['tmp_intId'],

			'autoincrement' => 'tmp_intId',

			'nullable'		=> ['tmp_intId', 'tmp_varCodigoDian', 'tmp_dtimFechaCreacion', 'tmp_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'tmp_intId' => ['type' => 'int'],
				'tmp_varCodigo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'tmp_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'tmp_lonDescripcion' => ['type' => 'str', 'required' => true],
				'tmp_varCodigoDian' => ['type' => 'str', 'max' => 5],
				'tmp_dtimFechaCreacion' => ['type' => 'datetime'],
				'tmp_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_medio_pago.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_medio_pago.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_medio_pago.usu_intIdCreador']
				],
				'tbl_factura' => [
					['tbl_factura.tmp_intIdMedioPago','tbl_medio_pago.tmp_intId']
				],
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.tmp_intIdMedioPago','tbl_medio_pago.tmp_intId']
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
				        0 => 'tbl_medio_pago',
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
				        0 => 'tbl_medio_pago',
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
				        0 => 'tbl_medio_pago',
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
				        1 => 'tmp_intIdMedioPago',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_medio_pago',
				        1 => 'tmp_intId',
				      ),
				    ),
				  ),
				  'tbl_empresa_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'tmp_intIdMedioPago',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_medio_pago',
				        1 => 'tmp_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_medio_pago.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_medio_pago.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_medio_pago.usu_intIdCreador']
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
				        0 => 'tbl_medio_pago',
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
				        0 => 'tbl_medio_pago',
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
				        0 => 'tbl_medio_pago',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblDescuentoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_descuento',

			'id_name'		=> 'des_intId',

			'attr_types'	=> [
				'des_intId' => 'INT',
				'des_varDescuento' => 'STR',
				'des_decDescuento' => 'STR',
				'des_dtimFechaCreacion' => 'STR',
				'des_timFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['des_intId'],

			'autoincrement' => 'des_intId',

			'nullable'		=> ['des_intId', 'des_dtimFechaCreacion', 'des_timFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> ['des_varDescuento', 'des_decDescuento'],

			'rules' 		=> [
				'des_intId' => ['type' => 'int'],
				'des_varDescuento' => ['type' => 'str', 'max' => 100, 'required' => true],
				'des_decDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'des_dtimFechaCreacion' => ['type' => 'datetime'],
				'des_timFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_descuento.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_descuento.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_descuento.usu_intIdCreador']
				],
				'tbl_cliente' => [
					['tbl_cliente.des_intIdDescuento','tbl_descuento.des_intId']
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
				        0 => 'tbl_descuento',
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
				        0 => 'tbl_descuento',
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
				        0 => 'tbl_descuento',
				        1 => 'usu_intIdCreador',
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
				        1 => 'des_intIdDescuento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_descuento',
				        1 => 'des_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_descuento.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_descuento.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_descuento.usu_intIdCreador']
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
				        0 => 'tbl_descuento',
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
				        0 => 'tbl_descuento',
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
				        0 => 'tbl_descuento',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


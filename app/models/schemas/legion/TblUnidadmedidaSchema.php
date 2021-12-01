<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblUnidadmedidaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_unidadmedida',

			'id_name'		=> 'unm_intId',

			'attr_types'	=> [
				'unm_intId' => 'INT',
				'unm_varUnidadMedida' => 'STR',
				'unm_dtimFechaCreacion' => 'STR',
				'unm_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['unm_intId'],

			'autoincrement' => 'unm_intId',

			'nullable'		=> ['unm_intId', 'unm_dtimFechaCreacion', 'unm_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'unm_intId' => ['type' => 'int'],
				'unm_varUnidadMedida' => ['type' => 'str', 'max' => 50, 'required' => true],
				'unm_dtimFechaCreacion' => ['type' => 'datetime'],
				'unm_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_unidadmedida.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_unidadmedida.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_unidadmedida.usu_intIdCreador']
				],
				'tbl_producto' => [
					['tbl_producto.unm_intIdUnidadMedida','tbl_unidadmedida.unm_intId']
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
				        0 => 'tbl_unidadmedida',
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
				        0 => 'tbl_unidadmedida',
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
				        0 => 'tbl_unidadmedida',
				        1 => 'usu_intIdCreador',
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
				        1 => 'unm_intIdUnidadMedida',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_unidadmedida',
				        1 => 'unm_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_unidadmedida.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_unidadmedida.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_unidadmedida.usu_intIdCreador']
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
				        0 => 'tbl_unidadmedida',
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
				        0 => 'tbl_unidadmedida',
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
				        0 => 'tbl_unidadmedida',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRhSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_rh',

			'id_name'		=> 'trh_intId',

			'attr_types'	=> [
				'trh_intId' => 'INT',
				'trh_varCodigo' => 'STR',
				'trh_varDescripcion' => 'STR',
				'trh_dtimFechaCreacion' => 'STR',
				'trh_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['trh_intId'],

			'autoincrement' => 'trh_intId',

			'nullable'		=> ['trh_intId', 'trh_dtimFechaCreacion', 'trh_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'trh_intId' => ['type' => 'int'],
				'trh_varCodigo' => ['type' => 'str', 'max' => 30, 'required' => true],
				'trh_varDescripcion' => ['type' => 'str', 'max' => 250, 'required' => true],
				'trh_dtimFechaCreacion' => ['type' => 'datetime'],
				'trh_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rh.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rh.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rh.usu_intIdActualizador']
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
				        0 => 'tbl_rh',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rh',
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
				        0 => 'tbl_rh',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rh.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rh.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rh.usu_intIdActualizador']
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
				        0 => 'tbl_rh',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_rh',
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
				        0 => 'tbl_rh',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


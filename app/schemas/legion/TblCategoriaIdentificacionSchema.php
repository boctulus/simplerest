<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCategoriaIdentificacionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_categoria_identificacion',

			'id_name'		=> 'cid_intId',

			'attr_types'	=> [
				'cid_intId' => 'INT',
				'cid_varCategoriaDocumento' => 'STR',
				'cid_varSiglas' => 'STR',
				'cid_dtimFechaCreacion' => 'STR',
				'cid_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cid_intId'],

			'autoincrement' => 'cid_intId',

			'nullable'		=> ['cid_intId', 'cid_dtimFechaCreacion', 'cid_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'cid_intId' => ['type' => 'int'],
				'cid_varCategoriaDocumento' => ['type' => 'str', 'max' => 100, 'required' => true],
				'cid_varSiglas' => ['type' => 'str', 'max' => 3, 'required' => true],
				'cid_dtimFechaCreacion' => ['type' => 'datetime'],
				'cid_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_identificacion.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_identificacion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_identificacion.usu_intIdCreador']
				],
				'tbl_persona' => [
					['tbl_persona.cid_intIdCategoriIdentificacion','tbl_categoria_identificacion.cid_intId']
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
				        0 => 'tbl_categoria_identificacion',
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
				        0 => 'tbl_categoria_identificacion',
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
				        0 => 'tbl_categoria_identificacion',
				        1 => 'usu_intIdCreador',
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
				        1 => 'cid_intIdCategoriIdentificacion',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_categoria_identificacion',
				        1 => 'cid_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_identificacion.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_identificacion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_identificacion.usu_intIdCreador']
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
				        0 => 'tbl_categoria_identificacion',
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
				        0 => 'tbl_categoria_identificacion',
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
				        0 => 'tbl_categoria_identificacion',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


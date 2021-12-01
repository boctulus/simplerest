<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEstratoEconomicoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_estrato_economico',

			'id_name'		=> 'tec_intId',

			'attr_types'	=> [
				'tec_intId' => 'INT',
				'tec_varCodigo' => 'STR',
				'tec_varDescripcion' => 'STR',
				'tec_dtimFechaCreacion' => 'STR',
				'tec_dtimFechaActualizacion' => 'STR',
				'est_intIdestado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['tec_intId'],

			'autoincrement' => 'tec_intId',

			'nullable'		=> ['tec_intId', 'tec_dtimFechaCreacion', 'tec_dtimFechaActualizacion', 'est_intIdestado'],

			'uniques'		=> [],

			'rules' 		=> [
				'tec_intId' => ['type' => 'int'],
				'tec_varCodigo' => ['type' => 'str', 'max' => 20, 'required' => true],
				'tec_varDescripcion' => ['type' => 'str', 'max' => 250, 'required' => true],
				'tec_dtimFechaCreacion' => ['type' => 'datetime'],
				'tec_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdestado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdestado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_estrato_economico.est_intIdestado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_estrato_economico.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_estrato_economico.usu_intIdCreador']
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
				        0 => 'tbl_estrato_economico',
				        1 => 'est_intIdestado',
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
				        0 => 'tbl_estrato_economico',
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
				        0 => 'tbl_estrato_economico',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_estrato_economico.est_intIdestado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_estrato_economico.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_estrato_economico.usu_intIdCreador']
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
				        0 => 'tbl_estrato_economico',
				        1 => 'est_intIdestado',
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
				        0 => 'tbl_estrato_economico',
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
				        0 => 'tbl_estrato_economico',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


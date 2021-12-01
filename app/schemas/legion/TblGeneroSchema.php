<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblGeneroSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_genero',

			'id_name'		=> 'gen_intId',

			'attr_types'	=> [
				'gen_intId' => 'INT',
				'gen_varGenero' => 'STR',
				'gen_dtimFechaCreacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT',
				'gen_dtimFechaActualizacion' => 'STR'
			],

			'primary'		=> ['gen_intId'],

			'autoincrement' => 'gen_intId',

			'nullable'		=> ['gen_intId', 'gen_dtimFechaCreacion', 'est_intIdEstado', 'gen_dtimFechaActualizacion'],

			'uniques'		=> ['gen_varGenero'],

			'rules' 		=> [
				'gen_intId' => ['type' => 'int'],
				'gen_varGenero' => ['type' => 'str', 'max' => 50, 'required' => true],
				'gen_dtimFechaCreacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true],
				'gen_dtimFechaActualizacion' => ['type' => 'datetime']
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_genero.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_genero.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_genero.usu_intIdCreador']
				],
				'tbl_persona' => [
					['tbl_persona.gen_intIdGenero','tbl_genero.gen_intId']
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
				        0 => 'tbl_genero',
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
				        0 => 'tbl_genero',
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
				        0 => 'tbl_genero',
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
				        1 => 'gen_intIdGenero',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_genero',
				        1 => 'gen_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_genero.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_genero.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_genero.usu_intIdCreador']
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
				        0 => 'tbl_genero',
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
				        0 => 'tbl_genero',
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
				        0 => 'tbl_genero',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


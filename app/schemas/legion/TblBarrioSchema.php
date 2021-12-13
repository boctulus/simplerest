<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblBarrioSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_barrio',

			'id_name'		=> 'bar_intId',

			'attr_types'	=> [
				'bar_intId' => 'INT',
				'bar_varCodigo' => 'STR',
				'bar_varNombre' => 'STR',
				'bar_lonDescripcion' => 'STR',
				'bar_dtimFechaCreacion' => 'STR',
				'bar_dtimFechaActualizacion' => 'STR',
				'ciu_intIdciudad' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['bar_intId'],

			'autoincrement' => 'bar_intId',

			'nullable'		=> ['bar_intId', 'bar_dtimFechaCreacion', 'bar_dtimFechaActualizacion', 'ciu_intIdciudad', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'bar_intId' => ['type' => 'int'],
				'bar_varCodigo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'bar_varNombre' => ['type' => 'str', 'max' => 150, 'required' => true],
				'bar_lonDescripcion' => ['type' => 'str', 'required' => true],
				'bar_dtimFechaCreacion' => ['type' => 'datetime'],
				'bar_dtimFechaActualizacion' => ['type' => 'datetime'],
				'ciu_intIdciudad' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['ciu_intIdciudad', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_ciudad' => [
					['tbl_ciudad.ciu_intId','tbl_barrio.ciu_intIdciudad']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_barrio.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_barrio.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_barrio.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_ciudad' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_barrio',
				        1 => 'ciu_intIdciudad',
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
				        0 => 'tbl_barrio',
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
				        0 => 'tbl_barrio',
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
				        0 => 'tbl_barrio',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_ciudad' => [
					['tbl_ciudad.ciu_intId','tbl_barrio.ciu_intIdciudad']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_barrio.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_barrio.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_barrio.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_ciudad' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_barrio',
				        1 => 'ciu_intIdciudad',
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
				        0 => 'tbl_barrio',
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
				        0 => 'tbl_barrio',
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
				        0 => 'tbl_barrio',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


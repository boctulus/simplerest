<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblClaseCuentaContableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_clase_cuenta_contable',

			'id_name'		=> 'cla_intId',

			'attr_types'	=> [
				'cla_intId' => 'INT',
				'cla_intCodigo' => 'INT',
				'cla_varNombre' => 'STR',
				'cla_dtimFechaCreacion' => 'STR',
				'cla_dtimFechaActualizacion' => 'STR',
				'nat_intId' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cla_intId', 'cla_intCodigo'],

			'autoincrement' => 'cla_intId',

			'nullable'		=> ['cla_intId', 'cla_dtimFechaCreacion', 'cla_dtimFechaActualizacion', 'nat_intId', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'cla_intId' => ['type' => 'int'],
				'cla_intCodigo' => ['type' => 'int', 'required' => true],
				'cla_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'cla_dtimFechaCreacion' => ['type' => 'datetime'],
				'cla_dtimFechaActualizacion' => ['type' => 'datetime'],
				'nat_intId' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['nat_intId', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_naturaleza_cuenta_contable' => [
					['tbl_naturaleza_cuenta_contable.ncc_intId','tbl_clase_cuenta_contable.nat_intId']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_clase_cuenta_contable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_clase_cuenta_contable.usu_intIdCreador']
				],
				'tbl_grupo_cuenta_contable' => [
					['tbl_grupo_cuenta_contable.cla_intIdClase','tbl_clase_cuenta_contable.cla_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_naturaleza_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_naturaleza_cuenta_contable',
				        1 => 'ncc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_clase_cuenta_contable',
				        1 => 'nat_intId',
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
				        0 => 'tbl_clase_cuenta_contable',
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
				        0 => 'tbl_clase_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_grupo_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_grupo_cuenta_contable',
				        1 => 'cla_intIdClase',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_clase_cuenta_contable',
				        1 => 'cla_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_naturaleza_cuenta_contable' => [
					['tbl_naturaleza_cuenta_contable.ncc_intId','tbl_clase_cuenta_contable.nat_intId']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_clase_cuenta_contable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_clase_cuenta_contable.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_naturaleza_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_naturaleza_cuenta_contable',
				        1 => 'ncc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_clase_cuenta_contable',
				        1 => 'nat_intId',
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
				        0 => 'tbl_clase_cuenta_contable',
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
				        0 => 'tbl_clase_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


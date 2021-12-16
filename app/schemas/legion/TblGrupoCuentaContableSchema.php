<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblGrupoCuentaContableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_grupo_cuenta_contable',

			'id_name'		=> 'gru_intId',

			'attr_types'	=> [
				'gru_intId' => 'INT',
				'gru_intCodigo' => 'INT',
				'gru_varNombre' => 'STR',
				'gru_dtimFechaCreacion' => 'STR',
				'gru_dtimFechaActualizacion' => 'STR',
				'cla_intIdClase' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['gru_intId', 'gru_intCodigo'],

			'autoincrement' => 'gru_intId',

			'nullable'		=> ['gru_intId', 'gru_dtimFechaCreacion', 'gru_dtimFechaActualizacion', 'cla_intIdClase', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> ['gru_varNombre'],

			'rules' 		=> [
				'gru_intId' => ['type' => 'int'],
				'gru_intCodigo' => ['type' => 'int', 'required' => true],
				'gru_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'gru_dtimFechaCreacion' => ['type' => 'datetime'],
				'gru_dtimFechaActualizacion' => ['type' => 'datetime'],
				'cla_intIdClase' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cla_intIdClase', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_clase_cuenta_contable' => [
					['tbl_clase_cuenta_contable.cla_intId','tbl_grupo_cuenta_contable.cla_intIdClase']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_grupo_cuenta_contable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_grupo_cuenta_contable.usu_intIdCreador']
				],
				'tbl_cuenta_contable' => [
					['tbl_cuenta_contable.gru_intIdGrupoCategoriaCuentaContable','tbl_grupo_cuenta_contable.gru_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_clase_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_clase_cuenta_contable',
				        1 => 'cla_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_grupo_cuenta_contable',
				        1 => 'cla_intIdClase',
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
				        0 => 'tbl_grupo_cuenta_contable',
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
				        0 => 'tbl_grupo_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cuenta_contable',
				        1 => 'gru_intIdGrupoCategoriaCuentaContable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_grupo_cuenta_contable',
				        1 => 'gru_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_clase_cuenta_contable' => [
					['tbl_clase_cuenta_contable.cla_intId','tbl_grupo_cuenta_contable.cla_intIdClase']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_grupo_cuenta_contable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_grupo_cuenta_contable.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_clase_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_clase_cuenta_contable',
				        1 => 'cla_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_grupo_cuenta_contable',
				        1 => 'cla_intIdClase',
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
				        0 => 'tbl_grupo_cuenta_contable',
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
				        0 => 'tbl_grupo_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCuentaContableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cuenta_contable',

			'id_name'		=> 'cue_intId',

			'attr_types'	=> [
				'cue_intId' => 'INT',
				'cue_varNumeroCuenta' => 'STR',
				'cue_varNombreCuenta' => 'STR',
				'cue_tinCuentaBalance' => 'INT',
				'cue_tinCuentaResultado' => 'INT',
				'cue_dtimFechaCreacion' => 'STR',
				'cue_dtimFechaActualizacion' => 'STR',
				'ccc_intIdCategoriaCuentaContable' => 'INT',
				'est_intIdEstado' => 'INT',
				'gru_intId' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cue_intId', 'cue_varNumeroCuenta'],

			'autoincrement' => 'cue_intId',

			'nullable'		=> ['cue_intId', 'cue_tinCuentaBalance', 'cue_tinCuentaResultado', 'cue_dtimFechaCreacion', 'cue_dtimFechaActualizacion', 'ccc_intIdCategoriaCuentaContable', 'est_intIdEstado', 'gru_intId', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'cue_intId' => ['type' => 'int'],
				'cue_varNumeroCuenta' => ['type' => 'str', 'max' => 4, 'required' => true],
				'cue_varNombreCuenta' => ['type' => 'str', 'max' => 50, 'required' => true],
				'cue_tinCuentaBalance' => ['type' => 'bool'],
				'cue_tinCuentaResultado' => ['type' => 'bool'],
				'cue_dtimFechaCreacion' => ['type' => 'datetime'],
				'cue_dtimFechaActualizacion' => ['type' => 'datetime'],
				'ccc_intIdCategoriaCuentaContable' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'gru_intId' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cuenta_contable.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cuenta_contable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cuenta_contable.usu_intIdCreador']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.cue_intIdCuentaContable','tbl_cuenta_contable.cue_intId']
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
				        0 => 'tbl_cuenta_contable',
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
				        0 => 'tbl_cuenta_contable',
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
				        0 => 'tbl_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_sub_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'cue_intIdCuentaContable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cuenta_contable',
				        1 => 'cue_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cuenta_contable.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cuenta_contable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cuenta_contable.usu_intIdCreador']
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
				        0 => 'tbl_cuenta_contable',
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
				        0 => 'tbl_cuenta_contable',
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
				        0 => 'tbl_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCuentaContableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cuenta_contable',

			'id_name'		=> 'cue_varNumeroCuenta',

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

			'nullable'		=> ['cue_intId'],

			'rules' 		=> [
				'cue_varNumeroCuenta' => ['max' => 4],
				'cue_varNombreCuenta' => ['max' => 50]
			],

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
			]
		];
	}	
}


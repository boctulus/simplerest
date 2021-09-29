<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblTipoContratoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_tipo_contrato',

			'id_name'		=> 'tic_intId',

			'attr_types'	=> [
				'tic_intId' => 'INT',
				'tic_varNombre' => 'STR',
				'tic_varDescripcion' => 'STR',
				'tic_varCodigoDian' => 'STR',
				'tic_dtimFechaCreacion' => 'STR',
				'tic_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['tic_intId'],

			'rules' 		=> [
				'tic_varNombre' => ['max' => 100],
				'tic_varCodigoDian' => ['max' => 20]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_tipo_contrato.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_tipo_contrato.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_tipo_contrato.usu_intIdCreador']
				]
			]
		];
	}	
}


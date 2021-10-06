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

			'nullable'		=> ['tic_intId', 'tic_dtimFechaCreacion', 'tic_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'tic_intId' => ['type' => 'int'],
				'tic_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'tic_varDescripcion' => ['type' => 'str', 'required' => true],
				'tic_varCodigoDian' => ['type' => 'str', 'max' => 20, 'required' => true],
				'tic_dtimFechaCreacion' => ['type' => 'datetime'],
				'tic_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
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


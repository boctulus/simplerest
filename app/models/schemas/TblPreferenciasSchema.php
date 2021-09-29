<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPreferenciasSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_preferencias',

			'id_name'		=> 'tpf_intId',

			'attr_types'	=> [
				'tpf_intId' => 'INT',
				'tpf_varCodigo' => 'STR',
				'tpf_varNombre' => 'STR',
				'tpf_lonDescripcion' => 'STR',
				'tpf_bitUso' => 'INT',
				'tpf_varParametro' => 'STR',
				'tpf_varTipoDato' => 'STR',
				'tpf_dtimFechaCreacion' => 'STR',
				'tpf_dtimFechaActualizacion' => 'STR',
				'doc_intIdDocumento' => 'INT',
				'men_idId' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['tpf_intId'],

			'rules' 		=> [
				'tpf_varCodigo' => ['max' => 50],
				'tpf_varNombre' => ['max' => 250],
				'tpf_varParametro' => ['max' => 50],
				'tpf_varTipoDato' => ['max' => 50]
			],

			'relationships' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_preferencias.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_preferencias.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_preferencias.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_preferencias.usu_intIdCreador']
				]
			]
		];
	}	
}


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

			'nullable'		=> ['tpf_intId', 'tpf_bitUso', 'tpf_dtimFechaCreacion', 'tpf_dtimFechaActualizacion', 'doc_intIdDocumento', 'men_idId', 'est_intIdEstado'],

			'rules' 		=> [
				'tpf_intId' => ['type' => 'int'],
				'tpf_varCodigo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'tpf_varNombre' => ['type' => 'str', 'max' => 250, 'required' => true],
				'tpf_lonDescripcion' => ['type' => 'str', 'required' => true],
				'tpf_bitUso' => ['type' => 'int'],
				'tpf_varParametro' => ['type' => 'str', 'max' => 50, 'required' => true],
				'tpf_varTipoDato' => ['type' => 'str', 'max' => 50, 'required' => true],
				'tpf_dtimFechaCreacion' => ['type' => 'datetime'],
				'tpf_dtimFechaActualizacion' => ['type' => 'datetime'],
				'doc_intIdDocumento' => ['type' => 'int'],
				'men_idId' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
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


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCategoriaDocumentoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_categoria_documento',

			'id_name'		=> 'cdo_intId',

			'attr_types'	=> [
				'cdo_intId' => 'INT',
				'cdo_varCategoriaDocumento' => 'STR',
				'cdo_varSiglas' => 'STR',
				'cdo_dtimFechaCreacion' => 'STR',
				'cdo_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['cdo_intId', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'rules' 		=> [
				'cdo_varCategoriaDocumento' => ['max' => 50],
				'cdo_varSiglas' => ['max' => 3]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_documento.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_categoria_documento.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_categoria_documento.usu_intIdCreador'],
					['tbl_usuario.cdo_intIdCategoriaDocumento','tbl_categoria_documento.cdo_intId']
				]
			]
		];
	}	
}


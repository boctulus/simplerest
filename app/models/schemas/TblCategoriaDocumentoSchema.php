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

			'nullable'		=> ['cdo_intId', 'cdo_dtimFechaCreacion', 'cdo_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'rules' 		=> [
				'cdo_intId' => ['type' => 'int'],
				'cdo_varCategoriaDocumento' => ['type' => 'str', 'max' => 50, 'required' => true],
				'cdo_varSiglas' => ['type' => 'str', 'max' => 3, 'required' => true],
				'cdo_dtimFechaCreacion' => ['type' => 'datetime'],
				'cdo_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_documento.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_documento.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_documento.usu_intIdCreador'],
					['tbl_usuario.cdo_intIdCategoriaDocumento','tbl_categoria_documento.cdo_intId']
				]
			]
		];
	}	
}


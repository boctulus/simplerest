<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblTipoPersonaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_tipo_persona',

			'id_name'		=> 'tpr_intId',

			'attr_types'	=> [
				'tpr_intId' => 'INT',
				'tpr_varNombre' => 'STR',
				'tpr_dtimFechaCreacion' => 'STR',
				'tpr_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['tpr_intId', 'tpr_dtimFechaCreacion', 'tpr_dtimFechaActualizacion'],

			'rules' 		=> [
				'tpr_intId' => ['type' => 'int'],
				'tpr_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'tpr_dtimFechaCreacion' => ['type' => 'datetime'],
				'tpr_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_tipo_persona.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_tipo_persona.usu_intIdActualizador']
				],
				'tbl_persona' => [
					['tbl_persona.tpr_intIdTipoPersona','tbl_tipo_persona.tpr_intId']
				]
			]
		];
	}	
}

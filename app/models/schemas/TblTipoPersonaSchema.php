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

			'nullable'		=> ['tpr_intId', 'tpr_dtimFechaActualizacion'],

			'rules' 		=> [
				'tpr_varNombre' => ['max' => 100]
			],

			'relationships' => [
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_tipo_persona.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_tipo_persona.usu_intIdCreador']
				],
				'tbl_persona' => [
					['tbl_persona.tpr_intIdTipoPersona','tbl_tipo_persona.tpr_intId']
				]
			]
		];
	}	
}


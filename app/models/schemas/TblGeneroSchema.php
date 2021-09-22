<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblGeneroSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_genero',

			'id_name'		=> 'gen_intId',

			'attr_types'	=> [
				'gen_intId' => 'INT',
				'gen_varGenero' => 'STR',
				'gen_dtimFechaCreacion' => 'STR',
				'gen_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['gen_intId'],

			'rules' 		=> [
				'gen_varGenero' => ['max' => 50]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_genero.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_genero.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_genero.usu_intIdCreador']
				],
				'tbl_persona' => [
					['tbl_persona.gen_intIdGenero','tbl_genero.gen_intId']
				]
			]
		];
	}	
}


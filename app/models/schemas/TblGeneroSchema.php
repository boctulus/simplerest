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

			'nullable'		=> ['gen_intId', 'gen_dtimFechaCreacion', 'gen_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'gen_varGenero' => ['max' => 50],
				'gen_dtimFechaCreacion' => ['type' => 'datetime'],
				'gen_dtimFechaActualizacion' => ['type' => 'datetime']
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_genero.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_genero.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_genero.usu_intIdCreador']
				],
				'tbl_persona' => [
					['tbl_persona.gen_intIdGenero','tbl_genero.gen_intId']
				]
			]
		];
	}	
}


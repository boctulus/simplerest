<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblOperadorPilaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_operador_pila',

			'id_name'		=> 'opp_intId',

			'attr_types'	=> [
				'opp_intId' => 'INT',
				'opp_varCodigo' => 'STR',
				'opp_varDescripcion' => 'STR',
				'opp_dtimFechaCreacion' => 'STR',
				'opp_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['opp_intId'],

			'rules' 		=> [
				'opp_varCodigo' => ['max' => 50],
				'opp_varDescripcion' => ['max' => 300]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_operador_pila.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_operador_pila.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_operador_pila.usu_intIdCreador']
				],
				'tbl_empresa' => [
					['tbl_empresa.opp_intIdOperador','tbl_operador_pila.opp_intId']
				]
			]
		];
	}	
}


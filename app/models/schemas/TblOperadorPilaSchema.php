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

			'nullable'		=> ['opp_intId', 'opp_dtimFechaCreacion', 'opp_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'opp_intId' => ['type' => 'int'],
				'opp_varCodigo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'opp_varDescripcion' => ['type' => 'str', 'max' => 300, 'required' => true],
				'opp_dtimFechaCreacion' => ['type' => 'datetime'],
				'opp_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_operador_pila.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_operador_pila.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_operador_pila.usu_intIdCreador']
				],
				'tbl_empresa' => [
					['tbl_empresa.opp_intIdOperador','tbl_operador_pila.opp_intId']
				]
			]
		];
	}	
}


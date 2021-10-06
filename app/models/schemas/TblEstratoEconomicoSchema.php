<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEstratoEconomicoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_estrato_economico',

			'id_name'		=> 'tec_intId',

			'attr_types'	=> [
				'tec_intId' => 'INT',
				'tec_varCodigo' => 'STR',
				'tec_varDescripcion' => 'STR',
				'tec_dtimFechaCreacion' => 'STR',
				'tec_dtimFechaActualizacion' => 'STR',
				'est_intIdestado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['tec_intId', 'tec_dtimFechaCreacion', 'tec_dtimFechaActualizacion', 'est_intIdestado'],

			'rules' 		=> [
				'tec_intId' => ['type' => 'int'],
				'tec_varCodigo' => ['type' => 'str', 'max' => 20, 'required' => true],
				'tec_varDescripcion' => ['type' => 'str', 'max' => 250, 'required' => true],
				'tec_dtimFechaCreacion' => ['type' => 'datetime'],
				'tec_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdestado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_estrato_economico.est_intIdestado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_estrato_economico.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_estrato_economico.usu_intIdCreador']
				]
			]
		];
	}	
}


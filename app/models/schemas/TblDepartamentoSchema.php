<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblDepartamentoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_departamento',

			'id_name'		=> 'dep_intId',

			'attr_types'	=> [
				'dep_intId' => 'INT',
				'dep_varCodigoDepartamento' => 'STR',
				'dep_varDepartamento' => 'STR',
				'dep_dtimFechaCreacion' => 'STR',
				'dep_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'pai_intIdPais' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['dep_intId'],

			'rules' 		=> [
				'dep_varCodigoDepartamento' => ['max' => 50],
				'dep_varDepartamento' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_departamento.est_intIdEstado']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_departamento.pai_intIdPais']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_departamento.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_departamento.usu_intIdCreador']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.dep_intIdDepartamento','tbl_departamento.dep_intId']
				]
			]
		];
	}	
}


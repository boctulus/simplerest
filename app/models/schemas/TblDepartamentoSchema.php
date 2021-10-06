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

			'nullable'		=> ['dep_intId', 'dep_dtimFechaCreacion', 'dep_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'dep_intId' => ['type' => 'int'],
				'dep_varCodigoDepartamento' => ['type' => 'str', 'max' => 50, 'required' => true],
				'dep_varDepartamento' => ['type' => 'str', 'max' => 100, 'required' => true],
				'dep_dtimFechaCreacion' => ['type' => 'datetime'],
				'dep_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'pai_intIdPais' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_departamento.est_intIdEstado']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_departamento.pai_intIdPais']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_departamento.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_departamento.usu_intIdCreador']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.dep_intIdDepartamento','tbl_departamento.dep_intId']
				]
			]
		];
	}	
}


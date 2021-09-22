<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCiudadSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_ciudad',

			'id_name'		=> 'ciu_varCodigo',

			'attr_types'	=> [
				'ciu_intId' => 'INT',
				'ciu_varCodigo' => 'STR',
				'ciu_varCiudad' => 'STR',
				'ciu_varIndicativoTelefono' => 'STR',
				'ciu_dtimFechaCreacion' => 'STR',
				'ciu_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'pai_intIdPais' => 'INT',
				'dep_intIdDepartamento' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['ciu_intId'],

			'rules' 		=> [
				'ciu_varCodigo' => ['max' => 5],
				'ciu_varCiudad' => ['max' => 100],
				'ciu_varIndicativoTelefono' => ['max' => 3]
			],

			'relationships' => [
				'tbl_departamento' => [
					['tbl_departamento.dep_intId','tbl_ciudad.dep_intIdDepartamento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_ciudad.est_intIdEstado']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_ciudad.pai_intIdPais']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_ciudad.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_ciudad.usu_intIdCreador']
				],
				'tbl_persona' => [
					['tbl_persona.ciu_intIdCiudad','tbl_ciudad.ciu_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.ciu_intIdCiudad','tbl_ciudad.ciu_intId']
				]
			]
		];
	}	
}


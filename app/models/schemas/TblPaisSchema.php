<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPaisSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_pais',

			'id_name'		=> 'pai_varCodigo',

			'attr_types'	=> [
				'pai_intId' => 'INT',
				'pai_varCodigo' => 'STR',
				'pai_varPais' => 'STR',
				'pai_varCodigoPaisCelular' => 'STR',
				'pai_dtimFechaCreacion' => 'STR',
				'pai_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'pai_intIdMoneda' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['pai_intId'],

			'rules' 		=> [
				'pai_varCodigo' => ['max' => 4],
				'pai_varPais' => ['max' => 100],
				'pai_varCodigoPaisCelular' => ['max' => 3]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pais.est_intIdEstado']
				],
				'tbl_moneda' => [
					['tbl_moneda.mon_intId','tbl_pais.pai_intIdMoneda']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_pais.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_pais.usu_intIdCreador']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.pai_intIdPais','tbl_pais.pai_intId']
				],
				'tbl_persona' => [
					['tbl_persona.pai_intIdPais','tbl_pais.pai_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.pai_intIdPais','tbl_pais.pai_intId']
				],
				'tbl_departamento' => [
					['tbl_departamento.pai_intIdPais','tbl_pais.pai_intId']
				]
			]
		];
	}	
}


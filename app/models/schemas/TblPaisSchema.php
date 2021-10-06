<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPaisSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_pais',

			'id_name'		=> 'pai_intId',

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

			'nullable'		=> ['pai_intId', 'pai_dtimFechaCreacion', 'pai_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'pai_intId' => ['type' => 'int'],
				'pai_varCodigo' => ['type' => 'str', 'max' => 4, 'required' => true],
				'pai_varPais' => ['type' => 'str', 'max' => 100, 'required' => true],
				'pai_varCodigoPaisCelular' => ['type' => 'str', 'max' => 3, 'required' => true],
				'pai_dtimFechaCreacion' => ['type' => 'datetime'],
				'pai_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'pai_intIdMoneda' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pais.est_intIdEstado']
				],
				'tbl_moneda' => [
					['tbl_moneda.mon_intId','tbl_pais.pai_intIdMoneda']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_pais.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_pais.usu_intIdActualizador']
				],
				'tbl_contacto' => [
					['tbl_contacto.pai_intIdPais','tbl_pais.pai_intId']
				],
				'tbl_persona' => [
					['tbl_persona.pai_intIdPais','tbl_pais.pai_intId']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.pai_intIdPais','tbl_pais.pai_intId']
				],
				'tbl_departamento' => [
					['tbl_departamento.pai_intIdPais','tbl_pais.pai_intId']
				]
			]
		];
	}	
}


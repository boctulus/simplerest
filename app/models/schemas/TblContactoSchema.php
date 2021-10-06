<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblContactoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_contacto',

			'id_name'		=> 'con_intId',

			'attr_types'	=> [
				'con_intId' => 'INT',
				'con_varNombreContacto' => 'STR',
				'con_varEmail' => 'STR',
				'con_varCelular' => 'STR',
				'con_varDireccion' => 'STR',
				'con_varTelefono' => 'STR',
				'con_varExtension' => 'STR',
				'con_dtimFechaCreacion' => 'STR',
				'con_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'emp_intIdEmpresa' => 'INT',
				'car_intIdcargo' => 'INT',
				'ciu_intIdCiudad' => 'INT',
				'pai_intIdPais' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['con_intId', 'con_varDireccion', 'con_varTelefono', 'con_varExtension', 'con_dtimFechaCreacion', 'con_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'con_intId' => ['type' => 'int'],
				'con_varNombreContacto' => ['type' => 'str', 'max' => 250, 'required' => true],
				'con_varEmail' => ['type' => 'str', 'max' => 100, 'required' => true],
				'con_varCelular' => ['type' => 'str', 'max' => 15, 'required' => true],
				'con_varDireccion' => ['type' => 'str', 'max' => 250],
				'con_varTelefono' => ['type' => 'str', 'max' => 10],
				'con_varExtension' => ['type' => 'str', 'max' => 5],
				'con_dtimFechaCreacion' => ['type' => 'datetime'],
				'con_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'emp_intIdEmpresa' => ['type' => 'int', 'required' => true],
				'car_intIdcargo' => ['type' => 'int', 'required' => true],
				'ciu_intIdCiudad' => ['type' => 'int', 'required' => true],
				'pai_intIdPais' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_cargo' => [
					['tbl_cargo.car_intId','tbl_contacto.car_intIdcargo']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.ciu_intId','tbl_contacto.ciu_intIdCiudad']
				],
				'tbl_empresa' => [
					['tbl_empresa.emp_intId','tbl_contacto.emp_intIdEmpresa']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_contacto.est_intIdEstado']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_contacto.pai_intIdPais']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_contacto.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_contacto.usu_intIdActualizador']
				]
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCargoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cargo',

			'id_name'		=> 'car_intId',

			'attr_types'	=> [
				'car_intId' => 'INT',
				'car_varNombre' => 'STR',
				'car_varDescripcion' => 'STR',
				'car_dtimFechaCreacion' => 'STR',
				'car_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['car_intId', 'car_dtimFechaCreacion', 'car_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'car_intId' => ['type' => 'int'],
				'car_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'car_varDescripcion' => ['type' => 'str', 'max' => 100, 'required' => true],
				'car_dtimFechaCreacion' => ['type' => 'datetime'],
				'car_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cargo.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cargo.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cargo.usu_intIdCreador'],
					['tbl_usuario.car_intIdCargo','tbl_cargo.car_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.car_intIdcargo','tbl_cargo.car_intId']
				]
			]
		];
	}	
}

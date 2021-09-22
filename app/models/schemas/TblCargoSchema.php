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

			'nullable'		=> ['car_intId'],

			'rules' 		=> [
				'car_varNombre' => ['max' => 50],
				'car_varDescripcion' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cargo.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_cargo.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_cargo.usu_intIdCreador'],
					['tbl_usuario.car_intIdCargo','tbl_cargo.car_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.car_intIdcargo','tbl_cargo.car_intId']
				]
			]
		];
	}	
}


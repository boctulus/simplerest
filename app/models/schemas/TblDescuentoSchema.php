<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblDescuentoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_descuento',

			'id_name'		=> 'des_intId',

			'attr_types'	=> [
				'des_intId' => 'INT',
				'des_varDescuento' => 'STR',
				'des_decDescuento' => 'STR',
				'des_dtimFechaCreacion' => 'STR',
				'des_timFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['des_intId', 'des_dtimFechaCreacion', 'des_timFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'des_decDescuento' => ['type' => 'str', 'required' => true],
				'des_intId' => ['type' => 'int'],
				'des_varDescuento' => ['type' => 'str', 'max' => 100, 'required' => true],
				'des_dtimFechaCreacion' => ['type' => 'datetime'],
				'des_timFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_descuento.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_descuento.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_descuento.usu_intIdActualizador']
				],
				'tbl_cliente' => [
					['tbl_cliente.des_intIdDescuento','tbl_descuento.des_intId']
				]
			]
		];
	}	
}


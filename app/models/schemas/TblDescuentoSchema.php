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

			'nullable'		=> ['des_intId'],

			'rules' 		=> [
				'des_varDescuento' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_descuento.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_descuento.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_descuento.usu_intIdCreador']
				],
				'tbl_cliente' => [
					['tbl_cliente.des_intIdDescuento','tbl_descuento.des_intId']
				]
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblMonedaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_moneda',

			'id_name'		=> 'mon_intId',

			'attr_types'	=> [
				'mon_intId' => 'INT',
				'mon_varCodigoMoneda' => 'STR',
				'mon_varDescripcion' => 'STR',
				'mon_dtimFechaCreacion' => 'STR',
				'mon_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['mon_intId'],

			'rules' 		=> [
				'mon_varCodigoMoneda' => ['max' => 50],
				'mon_varDescripcion' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_moneda.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_moneda.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_moneda.usu_intIdCreador']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.mon_intIdMoneda','tbl_moneda.mon_intId']
				],
				'tbl_producto' => [
					['tbl_producto.mon_intIdMoneda','tbl_moneda.mon_intId']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intIdMoneda','tbl_moneda.mon_intId']
				]
			]
		];
	}	
}


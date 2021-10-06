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

			'nullable'		=> ['mon_intId', 'mon_dtimFechaCreacion', 'mon_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'mon_intId' => ['type' => 'int'],
				'mon_varCodigoMoneda' => ['type' => 'str', 'max' => 50, 'required' => true],
				'mon_varDescripcion' => ['type' => 'str', 'max' => 100, 'required' => true],
				'mon_dtimFechaCreacion' => ['type' => 'datetime'],
				'mon_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_moneda.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_moneda.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_moneda.usu_intIdCreador']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intIdMoneda','tbl_moneda.mon_intId']
				],
				'tbl_producto' => [
					['tbl_producto.mon_intIdMoneda','tbl_moneda.mon_intId']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.mon_intIdMoneda','tbl_moneda.mon_intId']
				]
			]
		];
	}	
}


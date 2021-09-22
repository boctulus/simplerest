<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPeriodoPagoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_periodo_pago',

			'id_name'		=> 'pep_intId',

			'attr_types'	=> [
				'pep_intId' => 'INT',
				'pep_varNombre' => 'STR',
				'pep_dtimFechaCreacion' => 'STR',
				'pep_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['pep_intId'],

			'rules' 		=> [
				'pep_varNombre' => ['max' => 50]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_periodo_pago.est_intIdEstado']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_periodo_pago.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_periodo_pago.usu_intIdCreador']
				]
			]
		];
	}	
}


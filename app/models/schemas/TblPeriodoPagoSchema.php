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

			'nullable'		=> ['pep_intId', 'pep_dtimFechaCreacion', 'pep_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'pep_intId' => ['type' => 'int'],
				'pep_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'pep_dtimFechaCreacion' => ['type' => 'datetime'],
				'pep_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_periodo_pago.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_periodo_pago.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_periodo_pago.usu_intIdCreador']
				]
			]
		];
	}	
}


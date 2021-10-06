<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblTipoCuentaBancariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_tipo_cuenta_bancaria',

			'id_name'		=> 'tcb_intId',

			'attr_types'	=> [
				'tcb_intId' => 'INT',
				'tcb_varDescripcion' => 'STR',
				'tcb_dtimFechaCreacion' => 'STR',
				'tcb_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['tcb_intId', 'tcb_dtimFechaCreacion', 'tcb_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'tcb_intId' => ['type' => 'int'],
				'tcb_varDescripcion' => ['type' => 'str', 'max' => 50, 'required' => true],
				'tcb_dtimFechaCreacion' => ['type' => 'datetime'],
				'tcb_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_tipo_cuenta_bancaria.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_tipo_cuenta_bancaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_tipo_cuenta_bancaria.usu_intIdCreador']
				]
			]
		];
	}	
}


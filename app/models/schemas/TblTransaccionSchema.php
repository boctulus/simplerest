<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblTransaccionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_transaccion',

			'id_name'		=> 'tra_intId',

			'attr_types'	=> [
				'tra_intId' => 'INT',
				'tra_varTransaccion' => 'STR',
				'tra_bolEstado' => 'INT',
				'tra_dtimFechaActualizacion' => 'STR',
				'tra_dtimFechaCreacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['tra_intId'],

			'rules' 		=> [
				'tra_varTransaccion' => ['max' => 25]
			],

			'relationships' => [
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_transaccion.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_transaccion.usu_intIdCreador']
				],
				'tbl_documento' => [
					['tbl_documento.tra_intIdTransaccion','tbl_transaccion.tra_intId']
				]
			]
		];
	}	
}


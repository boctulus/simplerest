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

			'nullable'		=> ['tra_intId', 'tra_dtimFechaActualizacion', 'tra_dtimFechaCreacion'],

			'rules' 		=> [
				'tra_intId' => ['type' => 'int'],
				'tra_varTransaccion' => ['type' => 'str', 'max' => 25, 'required' => true],
				'tra_bolEstado' => ['type' => 'bool', 'required' => true],
				'tra_dtimFechaActualizacion' => ['type' => 'datetime'],
				'tra_dtimFechaCreacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_transaccion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_transaccion.usu_intIdCreador']
				],
				'tbl_documento' => [
					['tbl_documento.tra_intIdTransaccion','tbl_transaccion.tra_intId']
				]
			]
		];
	}	
}

<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblBancoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_banco',

			'id_name'		=> 'ban_varCodigo',

			'attr_types'	=> [
				'ban_intId' => 'INT',
				'ban_varCodigo' => 'STR',
				'ban_varDescripcion' => 'STR',
				'ban_dtimFechaCreacion' => 'STR',
				'ban_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT',
				'sub_intIdCuentaCxC' => 'INT'
			],

			'nullable'		=> ['ban_intId'],

			'rules' 		=> [
				'ban_varCodigo' => ['max' => 4],
				'ban_varDescripcion' => ['max' => 50]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_banco.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_banco.sub_intIdCuentaCxC']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_banco.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_banco.usu_intIdCreador']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.ban_intIdBanco','tbl_banco.ban_intId']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.ban_intIdBanco','tbl_banco.ban_intId']
				]
			]
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblBancoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_banco',

			'id_name'		=> 'ban_intId',

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

			'nullable'		=> ['ban_intId', 'ban_dtimFechaCreacion', 'ban_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'ban_intId' => ['type' => 'int'],
				'ban_varCodigo' => ['type' => 'str', 'max' => 4, 'required' => true],
				'ban_varDescripcion' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ban_dtimFechaCreacion' => ['type' => 'datetime'],
				'ban_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true],
				'sub_intIdCuentaCxC' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_banco.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_banco.sub_intIdCuentaCxC']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_banco.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_banco.usu_intIdCreador']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.ban_intIdBanco','tbl_banco.ban_intId']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.ban_intIdBanco','tbl_banco.ban_intId']
				]
			]
		];
	}	
}


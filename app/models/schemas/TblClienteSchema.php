<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblClienteSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cliente',

			'id_name'		=> 'cli_intId',

			'attr_types'	=> [
				'cli_intId' => 'INT',
				'cli_intDiasGracia' => 'INT',
				'cli_decCupoCredito' => 'STR',
				'cli_bolBloqueadoMora' => 'INT',
				'cli_datFechaBloqueado' => 'STR',
				'cli_dtimFechaCreacion' => 'STR',
				'cli_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'dpa_intIdDiasPago' => 'INT',
				'des_intIdDescuento' => 'INT',
				'ali_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['cli_intId', 'cli_dtimFechaCreacion', 'cli_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'rules' 		=> [
				'cli_decCupoCredito' => ['type' => 'str', 'required' => true],
				'cli_intId' => ['type' => 'int'],
				'cli_intDiasGracia' => ['type' => 'int', 'required' => true],
				'cli_bolBloqueadoMora' => ['type' => 'bool', 'required' => true],
				'cli_datFechaBloqueado' => ['type' => 'date', 'required' => true],
				'cli_dtimFechaCreacion' => ['type' => 'datetime'],
				'cli_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'dpa_intIdDiasPago' => ['type' => 'int', 'required' => true],
				'des_intIdDescuento' => ['type' => 'int', 'required' => true],
				'ali_intIdPersona' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'relationships' => [
				'tbl_descuento' => [
					['tbl_descuento.des_intId','tbl_cliente.des_intIdDescuento']
				],
				'tbl_dias_pago' => [
					['tbl_dias_pago.dpa_intId','tbl_cliente.dpa_intIdDiasPago']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cliente.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_cliente.ali_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cliente.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cliente.usu_intIdCreador']
				],
				'tbl_cliente_informacion_tributaria' => [
					['tbl_cliente_informacion_tributaria.cli_intIdCliente','tbl_cliente.cli_intId'],
					['tbl_cliente_informacion_tributaria.cli_intIdCliente','tbl_cliente.cli_intId']
				]
			]
		];
	}	
}

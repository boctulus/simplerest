<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblClienteInformacionTributariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cliente_informacion_tributaria',

			'id_name'		=> 'tic_intId',

			'attr_types'	=> [
				'tic_intId' => 'INT',
				'tic_intGranContribuyente' => 'INT',
				'tic_intllevarContabilidad' => 'INT',
				'tic_intCalcularIca' => 'INT',
				'tic_dtimFechaCreacion' => 'STR',
				'tic_dtimFechaActualizacion' => 'STR',
				'sub_intIdSubcuentacontable' => 'INT',
				'cli_intIdCliente' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['tic_intId', 'sub_intIdSubcuentacontable', 'cli_intIdCliente'],

			'rules' 		=> [

			],

			'relationships' => [
				'tbl_cliente' => [
					['tbl_cliente.cli_intId','tbl_cliente_informacion_tributaria.cli_intIdCliente'],
					['tbl_cliente.cli_intId','tbl_cliente_informacion_tributaria.cli_intIdCliente']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cliente_informacion_tributaria.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_cliente_informacion_tributaria.sub_intIdSubcuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cliente_informacion_tributaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cliente_informacion_tributaria.usu_intIdCreador']
				]
			]
		];
	}	
}


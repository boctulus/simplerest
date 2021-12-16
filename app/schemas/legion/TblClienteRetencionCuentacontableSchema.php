<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblClienteRetencionCuentacontableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cliente_retencion_cuentacontable',

			'id_name'		=> 'rcl_intId',

			'attr_types'	=> [
				'rcl_intId' => 'INT',
				'rcl_intIdRetencion' => 'INT',
				'rcl_intIdCuentaContable' => 'INT',
				'rcl_dtimFechaCreacion' => 'STR',
				'rcl_dtimFechaActualizacion' => 'STR',
				'cli_intIdCliente' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['rcl_intId', 'rcl_intIdRetencion', 'rcl_intIdCuentaContable'],

			'autoincrement' => 'rcl_intId',

			'nullable'		=> ['rcl_intId', 'rcl_intIdRetencion', 'rcl_intIdCuentaContable', 'rcl_dtimFechaCreacion', 'rcl_dtimFechaActualizacion', 'cli_intIdCliente', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'rcl_intId' => ['type' => 'int'],
				'rcl_intIdRetencion' => ['type' => 'int'],
				'rcl_intIdCuentaContable' => ['type' => 'int'],
				'rcl_dtimFechaCreacion' => ['type' => 'datetime'],
				'rcl_dtimFechaActualizacion' => ['type' => 'datetime'],
				'cli_intIdCliente' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cli_intIdCliente', 'rcl_intIdRetencion', 'rcl_intIdCuentaContable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_cliente' => [
					['tbl_cliente.cli_intId','tbl_cliente_retencion_cuentacontable.cli_intIdCliente']
				],
				'tbl_retencion' => [
					['tbl_retencion.ret_intId','tbl_cliente_retencion_cuentacontable.rcl_intIdRetencion']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_cliente_retencion_cuentacontable.rcl_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cliente_retencion_cuentacontable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cliente_retencion_cuentacontable.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_cliente' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'cli_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'cli_intIdCliente',
				      ),
				    ),
				  ),
				  'tbl_retencion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'ret_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'rcl_intIdRetencion',
				      ),
				    ),
				  ),
				  'tbl_sub_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'rcl_intIdCuentaContable',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_cliente' => [
					['tbl_cliente.cli_intId','tbl_cliente_retencion_cuentacontable.cli_intIdCliente']
				],
				'tbl_retencion' => [
					['tbl_retencion.ret_intId','tbl_cliente_retencion_cuentacontable.rcl_intIdRetencion']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_cliente_retencion_cuentacontable.rcl_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cliente_retencion_cuentacontable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cliente_retencion_cuentacontable.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_cliente' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'cli_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'cli_intIdCliente',
				      ),
				    ),
				  ),
				  'tbl_retencion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'ret_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'rcl_intIdRetencion',
				      ),
				    ),
				  ),
				  'tbl_sub_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'rcl_intIdCuentaContable',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


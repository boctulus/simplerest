<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblClienteReteivaCuentacontableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cliente_reteiva_cuentacontable',

			'id_name'		=> 'ric_intId',

			'attr_types'	=> [
				'ric_intId' => 'INT',
				'ric_intIdReteiva' => 'INT',
				'ric_intIdCuentacontable' => 'INT',
				'ric_dtimFechaCreacion' => 'STR',
				'ric_dtimFechaActualizacion' => 'STR',
				'cli_intIdCliente' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ric_intId', 'ric_intIdReteiva', 'ric_intIdCuentacontable'],

			'autoincrement' => 'ric_intId',

			'nullable'		=> ['ric_intId', 'ric_intIdReteiva', 'ric_intIdCuentacontable', 'ric_dtimFechaCreacion', 'ric_dtimFechaActualizacion', 'cli_intIdCliente', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'ric_intId' => ['type' => 'int'],
				'ric_intIdReteiva' => ['type' => 'int'],
				'ric_intIdCuentacontable' => ['type' => 'int'],
				'ric_dtimFechaCreacion' => ['type' => 'datetime'],
				'ric_dtimFechaActualizacion' => ['type' => 'datetime'],
				'cli_intIdCliente' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cli_intIdCliente', 'ric_intIdReteiva', 'ric_intIdCuentacontable'],

			'relationships' => [
				'tbl_cliente' => [
					['tbl_cliente.cli_intId','tbl_cliente_reteiva_cuentacontable.cli_intIdCliente']
				],
				'tbl_reteiva' => [
					['tbl_reteiva.riv_intId','tbl_cliente_reteiva_cuentacontable.ric_intIdReteiva']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_cliente_reteiva_cuentacontable.ric_intIdCuentacontable']
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
				        0 => 'tbl_cliente_reteiva_cuentacontable',
				        1 => 'cli_intIdCliente',
				      ),
				    ),
				  ),
				  'tbl_reteiva' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_reteiva',
				        1 => 'riv_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_reteiva_cuentacontable',
				        1 => 'ric_intIdReteiva',
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
				        0 => 'tbl_cliente_reteiva_cuentacontable',
				        1 => 'ric_intIdCuentacontable',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_cliente' => [
					['tbl_cliente.cli_intId','tbl_cliente_reteiva_cuentacontable.cli_intIdCliente']
				],
				'tbl_reteiva' => [
					['tbl_reteiva.riv_intId','tbl_cliente_reteiva_cuentacontable.ric_intIdReteiva']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_cliente_reteiva_cuentacontable.ric_intIdCuentacontable']
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
				        0 => 'tbl_cliente_reteiva_cuentacontable',
				        1 => 'cli_intIdCliente',
				      ),
				    ),
				  ),
				  'tbl_reteiva' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_reteiva',
				        1 => 'riv_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_reteiva_cuentacontable',
				        1 => 'ric_intIdReteiva',
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
				        0 => 'tbl_cliente_reteiva_cuentacontable',
				        1 => 'ric_intIdCuentacontable',
				      ),
				    ),
				  ),
				)
		];
	}	
}


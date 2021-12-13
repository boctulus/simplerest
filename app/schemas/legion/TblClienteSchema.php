<?php

namespace simplerest\schemas\legion;

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
				'per_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cli_intId'],

			'autoincrement' => 'cli_intId',

			'nullable'		=> ['cli_intId', 'cli_dtimFechaCreacion', 'cli_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> ['per_intIdPersona'],

			'rules' 		=> [
				'cli_intId' => ['type' => 'int'],
				'cli_intDiasGracia' => ['type' => 'int', 'required' => true],
				'cli_decCupoCredito' => ['type' => 'decimal(18,2)', 'required' => true],
				'cli_bolBloqueadoMora' => ['type' => 'bool', 'required' => true],
				'cli_datFechaBloqueado' => ['type' => 'date', 'required' => true],
				'cli_dtimFechaCreacion' => ['type' => 'datetime'],
				'cli_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'dpa_intIdDiasPago' => ['type' => 'int', 'required' => true],
				'des_intIdDescuento' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['des_intIdDescuento', 'dpa_intIdDiasPago', 'est_intIdEstado', 'per_intIdPersona', 'usu_intIdCreador', 'usu_intIdActualizador'],

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
					['tbl_persona.per_intId','tbl_cliente.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cliente.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cliente.usu_intIdCreador']
				],
				'tbl_cliente_retencion_cuentacontable' => [
					['tbl_cliente_retencion_cuentacontable.cli_intIdCliente','tbl_cliente.cli_intId']
				],
				'tbl_cliente_reteiva_cuentacontable' => [
					['tbl_cliente_reteiva_cuentacontable.cli_intIdCliente','tbl_cliente.cli_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_descuento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_descuento',
				        1 => 'des_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'des_intIdDescuento',
				      ),
				    ),
				  ),
				  'tbl_dias_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_dias_pago',
				        1 => 'dpa_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'dpa_intIdDiasPago',
				      ),
				    ),
				  ),
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'per_intIdPersona',
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
				        0 => 'tbl_cliente',
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
				        0 => 'tbl_cliente',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_cliente_retencion_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'cli_intIdCliente',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'cli_intId',
				      ),
				    ),
				  ),
				  'tbl_cliente_reteiva_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente_reteiva_cuentacontable',
				        1 => 'cli_intIdCliente',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'cli_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
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
					['tbl_persona.per_intId','tbl_cliente.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cliente.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cliente.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_descuento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_descuento',
				        1 => 'des_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'des_intIdDescuento',
				      ),
				    ),
				  ),
				  'tbl_dias_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_dias_pago',
				        1 => 'dpa_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'dpa_intIdDiasPago',
				      ),
				    ),
				  ),
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'per_intIdPersona',
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
				        0 => 'tbl_cliente',
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
				        0 => 'tbl_cliente',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


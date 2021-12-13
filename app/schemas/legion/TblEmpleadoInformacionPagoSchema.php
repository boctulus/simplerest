<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEmpleadoInformacionPagoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_empleado_informacion_pago',

			'id_name'		=> 'eip_intId',

			'attr_types'	=> [
				'eip_intId' => 'INT',
				'eip_intNumeroCuenta' => 'INT',
				'ban_intId' => 'INT',
				'tcb_intIdTipoCuenta' => 'INT',
				'per_intIdPersona' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['eip_intId'],

			'autoincrement' => 'eip_intId',

			'nullable'		=> ['eip_intId', 'eip_intNumeroCuenta', 'ban_intId', 'tcb_intIdTipoCuenta', 'per_intIdPersona', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'eip_intId' => ['type' => 'int'],
				'eip_intNumeroCuenta' => ['type' => 'int'],
				'ban_intId' => ['type' => 'int'],
				'tcb_intIdTipoCuenta' => ['type' => 'int'],
				'per_intIdPersona' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['ban_intId', 'est_intIdEstado', 'per_intIdPersona', 'tcb_intIdTipoCuenta', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_empleado_informacion_pago.ban_intId']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empleado_informacion_pago.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_empleado_informacion_pago.per_intIdPersona']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.tcb_intId','tbl_empleado_informacion_pago.tcb_intIdTipoCuenta']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_empleado_informacion_pago.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_empleado_informacion_pago.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_banco' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'ban_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'ban_intId',
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
				        0 => 'tbl_empleado_informacion_pago',
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
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_tipo_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'tcb_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'tcb_intIdTipoCuenta',
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
				        0 => 'tbl_empleado_informacion_pago',
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
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_empleado_informacion_pago.ban_intId']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empleado_informacion_pago.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_empleado_informacion_pago.per_intIdPersona']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.tcb_intId','tbl_empleado_informacion_pago.tcb_intIdTipoCuenta']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_empleado_informacion_pago.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_empleado_informacion_pago.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_banco' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'ban_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'ban_intId',
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
				        0 => 'tbl_empleado_informacion_pago',
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
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_tipo_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'tcb_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'tcb_intIdTipoCuenta',
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
				        0 => 'tbl_empleado_informacion_pago',
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
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


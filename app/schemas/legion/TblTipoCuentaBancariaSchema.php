<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblTipoCuentaBancariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_tipo_cuenta_bancaria',

			'id_name'		=> 'tcb_intId',

			'attr_types'	=> [
				'tcb_intId' => 'INT',
				'tcb_varCodigo' => 'STR',
				'tcb_varNombre' => 'STR',
				'tcb_lonDescripcion' => 'STR',
				'tcb_dtimFechaCreacion' => 'STR',
				'tcb_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['tcb_intId'],

			'autoincrement' => 'tcb_intId',

			'nullable'		=> ['tcb_intId', 'tcb_varCodigo', 'tcb_lonDescripcion', 'tcb_dtimFechaCreacion', 'tcb_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'tcb_intId' => ['type' => 'int'],
				'tcb_varCodigo' => ['type' => 'str', 'max' => 100],
				'tcb_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'tcb_lonDescripcion' => ['type' => 'str'],
				'tcb_dtimFechaCreacion' => ['type' => 'datetime'],
				'tcb_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_tipo_cuenta_bancaria.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_tipo_cuenta_bancaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_tipo_cuenta_bancaria.usu_intIdCreador']
				],
				'tbl_empleado_informacion_pago' => [
					['tbl_empleado_informacion_pago.tcb_intIdTipoCuenta','tbl_tipo_cuenta_bancaria.tcb_intId']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.tcb_intIdTipoCuentaBancaria','tbl_tipo_cuenta_bancaria.tcb_intId']
				],
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.tcb_intIdTipoCuenta','tbl_tipo_cuenta_bancaria.tcb_intId']
				]
			],

			'expanded_relationships' => array (
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
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_tipo_cuenta_bancaria',
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
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_empleado_informacion_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empleado_informacion_pago',
				        1 => 'tcb_intIdTipoCuenta',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'tcb_intId',
				      ),
				    ),
				  ),
				  'tbl_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'tcb_intIdTipoCuentaBancaria',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'tcb_intId',
				      ),
				    ),
				  ),
				  'tbl_empresa_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'tcb_intIdTipoCuenta',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'tcb_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_tipo_cuenta_bancaria.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_tipo_cuenta_bancaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_tipo_cuenta_bancaria.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
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
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_tipo_cuenta_bancaria',
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
				        0 => 'tbl_tipo_cuenta_bancaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


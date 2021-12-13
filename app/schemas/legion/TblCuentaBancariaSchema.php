<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCuentaBancariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cuenta_bancaria',

			'id_name'		=> 'cba_intId',

			'attr_types'	=> [
				'cba_intId' => 'INT',
				'cba_lonDescripcion' => 'STR',
				'cba_varNumeroCuenta' => 'STR',
				'cba_dtimFechaCreacion' => 'STR',
				'cba_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'ban_intIdBanco' => 'INT',
				'tcb_intIdTipoCuentaBancaria' => 'INT',
				'emp_intIdEmpresa' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cba_intId'],

			'autoincrement' => 'cba_intId',

			'nullable'		=> ['cba_intId', 'cba_dtimFechaCreacion', 'cba_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'cba_intId' => ['type' => 'int'],
				'cba_lonDescripcion' => ['type' => 'str', 'required' => true],
				'cba_varNumeroCuenta' => ['type' => 'str', 'max' => 11, 'required' => true],
				'cba_dtimFechaCreacion' => ['type' => 'datetime'],
				'cba_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'ban_intIdBanco' => ['type' => 'int', 'required' => true],
				'tcb_intIdTipoCuentaBancaria' => ['type' => 'int', 'required' => true],
				'emp_intIdEmpresa' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['ban_intIdBanco', 'emp_intIdEmpresa', 'est_intIdEstado', 'tcb_intIdTipoCuentaBancaria', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_cuenta_bancaria.ban_intIdBanco']
				],
				'tbl_empresa' => [
					['tbl_empresa.emp_intId','tbl_cuenta_bancaria.emp_intIdEmpresa']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cuenta_bancaria.est_intIdEstado']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.tcb_intId','tbl_cuenta_bancaria.tcb_intIdTipoCuentaBancaria']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cuenta_bancaria.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cuenta_bancaria.usu_intIdActualizador']
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
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'ban_intIdBanco',
				      ),
				    ),
				  ),
				  'tbl_empresa' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa',
				        1 => 'emp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'emp_intIdEmpresa',
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
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'tcb_intIdTipoCuentaBancaria',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_cuenta_bancaria.ban_intIdBanco']
				],
				'tbl_empresa' => [
					['tbl_empresa.emp_intId','tbl_cuenta_bancaria.emp_intIdEmpresa']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cuenta_bancaria.est_intIdEstado']
				],
				'tbl_tipo_cuenta_bancaria' => [
					['tbl_tipo_cuenta_bancaria.tcb_intId','tbl_cuenta_bancaria.tcb_intIdTipoCuentaBancaria']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cuenta_bancaria.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cuenta_bancaria.usu_intIdActualizador']
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
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'ban_intIdBanco',
				      ),
				    ),
				  ),
				  'tbl_empresa' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa',
				        1 => 'emp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'emp_intIdEmpresa',
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
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'tcb_intIdTipoCuentaBancaria',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cuenta_bancaria',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


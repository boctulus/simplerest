<?php

namespace simplerest\schemas\legion;

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
				'ban_varNombre' => 'STR',
				'ban_lonDescripcion' => 'STR',
				'ban_dtimFechaCreacion' => 'STR',
				'ban_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT',
				'sub_intIdCuentaCxC' => 'INT'
			],

			'primary'		=> ['ban_intId'],

			'autoincrement' => 'ban_intId',

			'nullable'		=> ['ban_intId', 'ban_varCodigo', 'ban_lonDescripcion', 'ban_dtimFechaCreacion', 'ban_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> ['ban_varCodigo'],

			'rules' 		=> [
				'ban_intId' => ['type' => 'int'],
				'ban_varCodigo' => ['type' => 'str', 'max' => 4],
				'ban_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'ban_lonDescripcion' => ['type' => 'str'],
				'ban_dtimFechaCreacion' => ['type' => 'datetime'],
				'ban_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int'],
				'sub_intIdCuentaCxC' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'sub_intIdCuentaCxC', 'usu_intIdActualizador', 'usu_intIdCreador'],

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
				'tbl_proveedor' => [
					['tbl_proveedor.ban_intIdBanco','tbl_banco.ban_intId']
				],
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.ban_intIdBanco','tbl_banco.ban_intId']
				],
				'tbl_empleado_informacion_pago' => [
					['tbl_empleado_informacion_pago.ban_intId','tbl_banco.ban_intId']
				],
				'tbl_cuenta_bancaria' => [
					['tbl_cuenta_bancaria.ban_intIdBanco','tbl_banco.ban_intId']
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
				        0 => 'tbl_banco',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_banco',
				        1 => 'sub_intIdCuentaCxC',
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
				        0 => 'tbl_banco',
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
				        0 => 'tbl_banco',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_proveedor' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_proveedor',
				        1 => 'ban_intIdBanco',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'ban_intId',
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
				        1 => 'ban_intIdBanco',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'ban_intId',
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
				        1 => 'ban_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'ban_intId',
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
				        1 => 'ban_intIdBanco',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'ban_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_banco.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_banco.sub_intIdCuentaCxC']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_banco.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_banco.usu_intIdCreador']
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
				        0 => 'tbl_banco',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_banco',
				        1 => 'sub_intIdCuentaCxC',
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
				        0 => 'tbl_banco',
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
				        0 => 'tbl_banco',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


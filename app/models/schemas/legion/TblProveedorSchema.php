<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblProveedorSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_proveedor',

			'id_name'		=> 'prv_intId',

			'attr_types'	=> [
				'prv_intId' => 'INT',
				'pro_intCuentaBancaria' => 'STR',
				'prv_dtimFechaCreacion' => 'STR',
				'prv_dtimFechaActualizacion' => 'STR',
				'dpa_intIdDiasPago' => 'INT',
				'ban_intIdBanco' => 'INT',
				'ccb_intIdCategoriaCuentaBancaria' => 'INT',
				'per_intIdPersona' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['prv_intId'],

			'autoincrement' => 'prv_intId',

			'nullable'		=> ['prv_intId', 'prv_dtimFechaCreacion', 'prv_dtimFechaActualizacion', 'per_intIdPersona', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'prv_intId' => ['type' => 'int'],
				'pro_intCuentaBancaria' => ['type' => 'str', 'max' => 15, 'required' => true],
				'prv_dtimFechaCreacion' => ['type' => 'datetime'],
				'prv_dtimFechaActualizacion' => ['type' => 'datetime'],
				'dpa_intIdDiasPago' => ['type' => 'int', 'required' => true],
				'ban_intIdBanco' => ['type' => 'int', 'required' => true],
				'ccb_intIdCategoriaCuentaBancaria' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['ban_intIdBanco', 'ccb_intIdCategoriaCuentaBancaria', 'dpa_intIdDiasPago', 'est_intIdEstado', 'per_intIdPersona', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_proveedor.ban_intIdBanco']
				],
				'tbl_categoria_cuenta_bancaria' => [
					['tbl_categoria_cuenta_bancaria.ccb_intId','tbl_proveedor.ccb_intIdCategoriaCuentaBancaria']
				],
				'tbl_dias_pago' => [
					['tbl_dias_pago.dpa_intId','tbl_proveedor.dpa_intIdDiasPago']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_proveedor.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_proveedor.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_proveedor.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_proveedor.usu_intIdCreador']
				],
				'tbl_proveedor_informacion_tributaria' => [
					['tbl_proveedor_informacion_tributaria.prv_intIdProveedor','tbl_proveedor.prv_intId']
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
				        0 => 'tbl_proveedor',
				        1 => 'ban_intIdBanco',
				      ),
				    ),
				  ),
				  'tbl_categoria_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_cuenta_bancaria',
				        1 => 'ccb_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_proveedor',
				        1 => 'ccb_intIdCategoriaCuentaBancaria',
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
				        0 => 'tbl_proveedor',
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
				        0 => 'tbl_proveedor',
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
				        0 => 'tbl_proveedor',
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
				        0 => 'tbl_proveedor',
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
				        0 => 'tbl_proveedor',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_proveedor_informacion_tributaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_proveedor_informacion_tributaria',
				        1 => 'prv_intIdProveedor',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_proveedor',
				        1 => 'prv_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_banco' => [
					['tbl_banco.ban_intId','tbl_proveedor.ban_intIdBanco']
				],
				'tbl_categoria_cuenta_bancaria' => [
					['tbl_categoria_cuenta_bancaria.ccb_intId','tbl_proveedor.ccb_intIdCategoriaCuentaBancaria']
				],
				'tbl_dias_pago' => [
					['tbl_dias_pago.dpa_intId','tbl_proveedor.dpa_intIdDiasPago']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_proveedor.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_proveedor.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_proveedor.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_proveedor.usu_intIdCreador']
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
				        0 => 'tbl_proveedor',
				        1 => 'ban_intIdBanco',
				      ),
				    ),
				  ),
				  'tbl_categoria_cuenta_bancaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_cuenta_bancaria',
				        1 => 'ccb_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_proveedor',
				        1 => 'ccb_intIdCategoriaCuentaBancaria',
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
				        0 => 'tbl_proveedor',
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
				        0 => 'tbl_proveedor',
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
				        0 => 'tbl_proveedor',
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
				        0 => 'tbl_proveedor',
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
				        0 => 'tbl_proveedor',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


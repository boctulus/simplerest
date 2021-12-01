<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblIvaCuentacontableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_iva_cuentacontable',

			'id_name'		=> 'ivc_intId',

			'attr_types'	=> [
				'ivc_intId' => 'INT',
				'ivc_intIdIva' => 'INT',
				'ivc_intIdCuentaContable' => 'INT',
				'ivc_dtimFechaCreacion' => 'STR',
				'ivc_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ivc_intId', 'ivc_intIdIva', 'ivc_intIdCuentaContable'],

			'autoincrement' => 'ivc_intId',

			'nullable'		=> ['ivc_intId', 'ivc_dtimFechaCreacion', 'ivc_dtimFechaActualizacion'],

			'uniques'		=> [],

			'rules' 		=> [
				'ivc_intId' => ['type' => 'int'],
				'ivc_intIdIva' => ['type' => 'int', 'required' => true],
				'ivc_intIdCuentaContable' => ['type' => 'int', 'required' => true],
				'ivc_dtimFechaCreacion' => ['type' => 'datetime'],
				'ivc_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['ivc_intIdIva', 'ivc_intIdCuentaContable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_iva' => [
					['tbl_iva.iva_intId','tbl_iva_cuentacontable.ivc_intIdIva']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_iva_cuentacontable.ivc_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_iva_cuentacontable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_iva_cuentacontable.usu_intIdCreador']
				],
				'tbl_llave_impuesto' => [
					['tbl_llave_impuesto.iva_intIdIvaCuentaContable','tbl_iva_cuentacontable.ivc_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_iva' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_iva',
				        1 => 'iva_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'ivc_intIdIva',
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
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'ivc_intIdCuentaContable',
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
				        0 => 'tbl_iva_cuentacontable',
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
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_llave_impuesto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_llave_impuesto',
				        1 => 'iva_intIdIvaCuentaContable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'ivc_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_iva' => [
					['tbl_iva.iva_intId','tbl_iva_cuentacontable.ivc_intIdIva']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_iva_cuentacontable.ivc_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_iva_cuentacontable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_iva_cuentacontable.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_iva' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_iva',
				        1 => 'iva_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'ivc_intIdIva',
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
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'ivc_intIdCuentaContable',
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
				        0 => 'tbl_iva_cuentacontable',
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
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


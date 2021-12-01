<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblProveedorInformacionTributariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_proveedor_informacion_tributaria',

			'id_name'		=> 'tip_intId',

			'attr_types'	=> [
				'tip_intId' => 'INT',
				'tip_dtimFechaCreacion' => 'STR',
				'tip_dtimFechaActualizacion' => 'STR',
				'sub_intIdSubCuentaContable' => 'INT',
				'prv_intIdProveedor' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['tip_intId'],

			'autoincrement' => 'tip_intId',

			'nullable'		=> ['tip_intId', 'tip_dtimFechaCreacion', 'tip_dtimFechaActualizacion', 'sub_intIdSubCuentaContable', 'prv_intIdProveedor', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'tip_intId' => ['type' => 'int'],
				'tip_dtimFechaCreacion' => ['type' => 'datetime'],
				'tip_dtimFechaActualizacion' => ['type' => 'datetime'],
				'sub_intIdSubCuentaContable' => ['type' => 'int'],
				'prv_intIdProveedor' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'prv_intIdProveedor', 'sub_intIdSubCuentaContable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_proveedor_informacion_tributaria.est_intIdEstado']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.prv_intId','tbl_proveedor_informacion_tributaria.prv_intIdProveedor']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_proveedor_informacion_tributaria.sub_intIdSubCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_proveedor_informacion_tributaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_proveedor_informacion_tributaria.usu_intIdCreador']
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
				        0 => 'tbl_proveedor_informacion_tributaria',
				        1 => 'est_intIdEstado',
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
				        1 => 'prv_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_proveedor_informacion_tributaria',
				        1 => 'prv_intIdProveedor',
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
				        0 => 'tbl_proveedor_informacion_tributaria',
				        1 => 'sub_intIdSubCuentaContable',
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
				        0 => 'tbl_proveedor_informacion_tributaria',
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
				        0 => 'tbl_proveedor_informacion_tributaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_proveedor_informacion_tributaria.est_intIdEstado']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.prv_intId','tbl_proveedor_informacion_tributaria.prv_intIdProveedor']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_proveedor_informacion_tributaria.sub_intIdSubCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_proveedor_informacion_tributaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_proveedor_informacion_tributaria.usu_intIdCreador']
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
				        0 => 'tbl_proveedor_informacion_tributaria',
				        1 => 'est_intIdEstado',
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
				        1 => 'prv_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_proveedor_informacion_tributaria',
				        1 => 'prv_intIdProveedor',
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
				        0 => 'tbl_proveedor_informacion_tributaria',
				        1 => 'sub_intIdSubCuentaContable',
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
				        0 => 'tbl_proveedor_informacion_tributaria',
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
				        0 => 'tbl_proveedor_informacion_tributaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


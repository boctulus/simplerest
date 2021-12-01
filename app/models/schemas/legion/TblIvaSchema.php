<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblIvaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_iva',

			'id_name'		=> 'iva_intId',

			'attr_types'	=> [
				'iva_intId' => 'INT',
				'iva_varIVA' => 'STR',
				'iva_intTope' => 'INT',
				'iva_decPorcentaje' => 'STR',
				'iva_dtimFechaCreacion' => 'STR',
				'iva_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT',
				'sub_intIdCuentaContable' => 'INT'
			],

			'primary'		=> ['iva_intId'],

			'autoincrement' => 'iva_intId',

			'nullable'		=> ['iva_intId', 'iva_dtimFechaCreacion', 'iva_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> ['iva_varIVA'],

			'rules' 		=> [
				'iva_intId' => ['type' => 'int'],
				'iva_varIVA' => ['type' => 'str', 'max' => 50, 'required' => true],
				'iva_intTope' => ['type' => 'int', 'required' => true],
				'iva_decPorcentaje' => ['type' => 'decimal(18,2)', 'required' => true],
				'iva_dtimFechaCreacion' => ['type' => 'datetime'],
				'iva_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true],
				'sub_intIdCuentaContable' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'sub_intIdCuentaContable', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_iva.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_iva.sub_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_iva.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_iva.usu_intIdActualizador']
				],
				'tbl_producto' => [
					['tbl_producto.iva_intIdIva','tbl_iva.iva_intId']
				],
				'tbl_iva_cuentacontable' => [
					['tbl_iva_cuentacontable.ivc_intIdIva','tbl_iva.iva_intId']
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
				        0 => 'tbl_iva',
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
				        0 => 'tbl_iva',
				        1 => 'sub_intIdCuentaContable',
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
				        0 => 'tbl_iva',
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
				        0 => 'tbl_iva',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				  'tbl_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'iva_intIdIva',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_iva',
				        1 => 'iva_intId',
				      ),
				    ),
				  ),
				  'tbl_iva_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_iva_cuentacontable',
				        1 => 'ivc_intIdIva',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_iva',
				        1 => 'iva_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_iva.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_iva.sub_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_iva.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_iva.usu_intIdActualizador']
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
				        0 => 'tbl_iva',
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
				        0 => 'tbl_iva',
				        1 => 'sub_intIdCuentaContable',
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
				        0 => 'tbl_iva',
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
				        0 => 'tbl_iva',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


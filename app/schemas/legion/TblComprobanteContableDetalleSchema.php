<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblComprobanteContableDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_comprobante_contable_detalle',

			'id_name'		=> 'cpd_intId',

			'attr_types'	=> [
				'cpd_intId' => 'INT',
				'cpd_varCuentaContable' => 'STR',
				'cpd_varTercero' => 'STR',
				'cpd_varCentroCostos' => 'STR',
				'cpd_decBase' => 'STR',
				'cpd_decCuentaCredito' => 'STR',
				'cpd_decCuentaDebito' => 'STR',
				'cpd_dtimFechaCreacion' => 'STR',
				'cpd_dtimFechaActualizacion' => 'STR',
				'sub_intIdCuentaContable' => 'INT',
				'cmp_intIdComprobanteContable' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cpd_intId'],

			'autoincrement' => 'cpd_intId',

			'nullable'		=> ['cpd_intId', 'cpd_dtimFechaCreacion', 'cpd_dtimFechaActualizacion', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'cpd_intId' => ['type' => 'int'],
				'cpd_varCuentaContable' => ['type' => 'str', 'max' => 20, 'required' => true],
				'cpd_varTercero' => ['type' => 'str', 'max' => 20, 'required' => true],
				'cpd_varCentroCostos' => ['type' => 'str', 'max' => 20, 'required' => true],
				'cpd_decBase' => ['type' => 'decimal(18,2)', 'required' => true],
				'cpd_decCuentaCredito' => ['type' => 'decimal(18,2)', 'required' => true],
				'cpd_decCuentaDebito' => ['type' => 'decimal(18,2)', 'required' => true],
				'cpd_dtimFechaCreacion' => ['type' => 'datetime'],
				'cpd_dtimFechaActualizacion' => ['type' => 'datetime'],
				'sub_intIdCuentaContable' => ['type' => 'int', 'required' => true],
				'cmp_intIdComprobanteContable' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cmp_intIdComprobanteContable', 'doc_intIdDocumento', 'sub_intIdCuentaContable', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_comprobante_contable' => [
					['tbl_comprobante_contable.cmp_intId','tbl_comprobante_contable_detalle.cmp_intIdComprobanteContable']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_comprobante_contable_detalle.doc_intIdDocumento']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_comprobante_contable_detalle.sub_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_comprobante_contable_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_comprobante_contable_detalle.usu_intIdActualizador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_comprobante_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable',
				        1 => 'cmp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'cmp_intIdComprobanteContable',
				      ),
				    ),
				  ),
				  'tbl_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_comprobante_contable_detalle',
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
				        0 => 'tbl_comprobante_contable_detalle',
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
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_comprobante_contable' => [
					['tbl_comprobante_contable.cmp_intId','tbl_comprobante_contable_detalle.cmp_intIdComprobanteContable']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_comprobante_contable_detalle.doc_intIdDocumento']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_comprobante_contable_detalle.sub_intIdCuentaContable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_comprobante_contable_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_comprobante_contable_detalle.usu_intIdActualizador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_comprobante_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable',
				        1 => 'cmp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'cmp_intIdComprobanteContable',
				      ),
				    ),
				  ),
				  'tbl_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'doc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_comprobante_contable_detalle',
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
				        0 => 'tbl_comprobante_contable_detalle',
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
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

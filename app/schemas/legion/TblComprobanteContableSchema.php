<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblComprobanteContableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_comprobante_contable',

			'id_name'		=> 'cmp_intId',

			'attr_types'	=> [
				'cmp_intId' => 'INT',
				'cmp_varNroDcoumento' => 'STR',
				'cmp_intIdDocumento' => 'INT',
				'cmp_dtmFechaMovimiento' => 'STR',
				'cmp_dtmFechaCreacion' => 'STR',
				'cmp_dtmFechaActualizacion' => 'STR',
				'cmp_decTotalCuentaCredito' => 'STR',
				'cmp_decTotalCuentaDebito' => 'STR',
				'cmp_decTotalDiferencia' => 'STR',
				'cmp_varNota' => 'STR',
				'doc_intIdDocumento' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cmp_intId'],

			'autoincrement' => 'cmp_intId',

			'nullable'		=> ['cmp_intId', 'cmp_varNroDcoumento', 'cmp_intIdDocumento', 'cmp_dtmFechaCreacion', 'cmp_dtmFechaActualizacion', 'cmp_decTotalCuentaCredito', 'cmp_decTotalCuentaDebito', 'cmp_decTotalDiferencia', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'cmp_intId' => ['type' => 'int'],
				'cmp_varNroDcoumento' => ['type' => 'str', 'max' => 20],
				'cmp_intIdDocumento' => ['type' => 'int'],
				'cmp_dtmFechaMovimiento' => ['type' => 'date', 'required' => true],
				'cmp_dtmFechaCreacion' => ['type' => 'datetime'],
				'cmp_dtmFechaActualizacion' => ['type' => 'datetime'],
				'cmp_decTotalCuentaCredito' => ['type' => 'decimal(10,0)'],
				'cmp_decTotalCuentaDebito' => ['type' => 'decimal(10,0)'],
				'cmp_decTotalDiferencia' => ['type' => 'decimal(10,0)'],
				'cmp_varNota' => ['type' => 'str', 'max' => 250, 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['doc_intIdDocumento', 'est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_comprobante_contable.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_comprobante_contable.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_comprobante_contable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_comprobante_contable.usu_intIdCreador']
				],
				'tbl_comprobante_contable_detalle' => [
					['tbl_comprobante_contable_detalle.cmp_intIdComprobanteContable','tbl_comprobante_contable.cmp_intId']
				]
			],

			'expanded_relationships' => array (
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
				        0 => 'tbl_comprobante_contable',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_comprobante_contable',
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
				        0 => 'tbl_comprobante_contable',
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
				        0 => 'tbl_comprobante_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_comprobante_contable_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'cmp_intIdComprobanteContable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_comprobante_contable',
				        1 => 'cmp_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_comprobante_contable.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_comprobante_contable.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_comprobante_contable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_comprobante_contable.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
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
				        0 => 'tbl_comprobante_contable',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_comprobante_contable',
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
				        0 => 'tbl_comprobante_contable',
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
				        0 => 'tbl_comprobante_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


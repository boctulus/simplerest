<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblConsecutivoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_consecutivo',

			'id_name'		=> 'cse_intId',

			'attr_types'	=> [
				'cse_intId' => 'INT',
				'cse_intConsecutivo' => 'INT',
				'cse_varPrefijo' => 'STR',
				'cse_intDesde' => 'INT',
				'cse_intHasta' => 'INT',
				'cse_dtimFechaCreacion' => 'STR',
				'cse_dtimFechaActualizacion' => 'STR',
				'doc_intIdDocumento' => 'INT',
				'res_intIdResolucion' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cse_intId'],

			'autoincrement' => 'cse_intId',

			'nullable'		=> ['cse_intId', 'cse_intConsecutivo', 'cse_varPrefijo', 'cse_dtimFechaCreacion', 'cse_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'cse_intId' => ['type' => 'int'],
				'cse_intConsecutivo' => ['type' => 'int'],
				'cse_varPrefijo' => ['type' => 'str', 'max' => 4],
				'cse_intDesde' => ['type' => 'int', 'required' => true],
				'cse_intHasta' => ['type' => 'int', 'required' => true],
				'cse_dtimFechaCreacion' => ['type' => 'datetime'],
				'cse_dtimFechaActualizacion' => ['type' => 'datetime'],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'res_intIdResolucion' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['doc_intIdDocumento', 'est_intIdEstado', 'res_intIdResolucion', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_consecutivo.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_consecutivo.est_intIdEstado']
				],
				'tbl_resolucion' => [
					['tbl_resolucion.res_intId','tbl_consecutivo.res_intIdResolucion']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_consecutivo.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_consecutivo.usu_intIdCreador']
				],
				'tbl_contrato' => [
					['tbl_contrato.cse_intidConsecutivo','tbl_consecutivo.cse_intId']
				],
				'tbl_mvto_inventario' => [
					['tbl_mvto_inventario.cse_intIdConsecutivo','tbl_consecutivo.cse_intId']
				],
				'tbl_compras' => [
					['tbl_compras.cse_intIdConsecutivo','tbl_consecutivo.cse_intId']
				],
				'tbl_orden_compra' => [
					['tbl_orden_compra.cse_intIdConsecutivo','tbl_consecutivo.cse_intId']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.cse_intIdConsecutivo','tbl_consecutivo.cse_intId']
				],
				'tbl_factura' => [
					['tbl_factura.cse_intIdConsecutivo','tbl_consecutivo.cse_intId']
				],
				'tbl_nota_debito' => [
					['tbl_nota_debito.cse_intIdConsecutivo','tbl_consecutivo.cse_intId']
				],
				'tbl_cotizacion' => [
					['tbl_cotizacion.cse_intIdConsecutivo','tbl_consecutivo.cse_intId']
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
				        0 => 'tbl_consecutivo',
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
				        0 => 'tbl_consecutivo',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_resolucion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_resolucion',
				        1 => 'res_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'res_intIdResolucion',
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
				        0 => 'tbl_consecutivo',
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
				        0 => 'tbl_consecutivo',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_contrato' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contrato',
				        1 => 'cse_intidConsecutivo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				    ),
				  ),
				  'tbl_mvto_inventario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_mvto_inventario',
				        1 => 'cse_intIdConsecutivo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				    ),
				  ),
				  'tbl_compras' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_compras',
				        1 => 'cse_intIdConsecutivo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				    ),
				  ),
				  'tbl_orden_compra' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_orden_compra',
				        1 => 'cse_intIdConsecutivo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_credito' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_credito',
				        1 => 'cse_intIdConsecutivo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				    ),
				  ),
				  'tbl_factura' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'cse_intIdConsecutivo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_debito' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_debito',
				        1 => 'cse_intIdConsecutivo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				    ),
				  ),
				  'tbl_cotizacion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion',
				        1 => 'cse_intIdConsecutivo',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_consecutivo.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_consecutivo.est_intIdEstado']
				],
				'tbl_resolucion' => [
					['tbl_resolucion.res_intId','tbl_consecutivo.res_intIdResolucion']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_consecutivo.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_consecutivo.usu_intIdCreador']
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
				        0 => 'tbl_consecutivo',
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
				        0 => 'tbl_consecutivo',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_resolucion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_resolucion',
				        1 => 'res_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'res_intIdResolucion',
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
				        0 => 'tbl_consecutivo',
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
				        0 => 'tbl_consecutivo',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


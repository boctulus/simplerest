<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCotizacionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cotizacion',

			'id_name'		=> 'cot_intId',

			'attr_types'	=> [
				'cot_intId' => 'INT',
				'cot_varNumeroDocumento' => 'STR',
				'cot_decCantidadTotal' => 'STR',
				'cot_decBruto' => 'STR',
				'cot_decDescuento' => 'STR',
				'cot_decIVA' => 'STR',
				'cot_dateFecha' => 'STR',
				'cot_decNeto' => 'STR',
				'cot_bolEstado' => 'INT',
				'cot_dateFechaVencimiento' => 'STR',
				'cot_dtimFechaCreacion' => 'STR',
				'cot_dtimFechaActualizacion' => 'STR',
				'cot_varNota' => 'STR',
				'doc_intIdDocumento' => 'INT',
				'cse_intIdConsecutivo' => 'INT',
				'per_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['cot_intId', 'cot_varNumeroDocumento'],

			'autoincrement' => 'cot_intId',

			'nullable'		=> ['cot_intId', 'cot_bolEstado', 'cot_dtimFechaCreacion', 'cot_dtimFechaActualizacion'],

			'uniques'		=> [],

			'rules' 		=> [
				'cot_intId' => ['type' => 'int'],
				'cot_varNumeroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'cot_decCantidadTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'cot_decBruto' => ['type' => 'decimal(18,2)', 'required' => true],
				'cot_decDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'cot_decIVA' => ['type' => 'decimal(18,2)', 'required' => true],
				'cot_dateFecha' => ['type' => 'date', 'required' => true],
				'cot_decNeto' => ['type' => 'decimal(18,2)', 'required' => true],
				'cot_bolEstado' => ['type' => 'bool'],
				'cot_dateFechaVencimiento' => ['type' => 'date', 'required' => true],
				'cot_dtimFechaCreacion' => ['type' => 'datetime'],
				'cot_dtimFechaActualizacion' => ['type' => 'datetime'],
				'cot_varNota' => ['type' => 'str', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'cse_intIdConsecutivo' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['cse_intIdConsecutivo', 'doc_intIdDocumento', 'per_intIdPersona', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_cotizacion.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_cotizacion.doc_intIdDocumento']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_cotizacion.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cotizacion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cotizacion.usu_intIdCreador']
				],
				'tbl_cotizacion_detalle' => [
					['tbl_cotizacion_detalle.cot_intIdCotizacion','tbl_cotizacion.cot_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_consecutivo' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cotizacion',
				        1 => 'cse_intIdConsecutivo',
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
				        0 => 'tbl_cotizacion',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_cotizacion',
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
				        0 => 'tbl_cotizacion',
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
				        0 => 'tbl_cotizacion',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_cotizacion_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cotizacion_detalle',
				        1 => 'cot_intIdCotizacion',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cotizacion',
				        1 => 'cot_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_cotizacion.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_cotizacion.doc_intIdDocumento']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_cotizacion.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cotizacion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cotizacion.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_consecutivo' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_consecutivo',
				        1 => 'cse_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cotizacion',
				        1 => 'cse_intIdConsecutivo',
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
				        0 => 'tbl_cotizacion',
				        1 => 'doc_intIdDocumento',
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
				        0 => 'tbl_cotizacion',
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
				        0 => 'tbl_cotizacion',
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
				        0 => 'tbl_cotizacion',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


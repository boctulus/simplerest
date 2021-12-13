<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblMvtoInventarioSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_mvto_inventario',

			'id_name'		=> 'mvi_intId',

			'attr_types'	=> [
				'mvi_intId' => 'INT',
				'mvi_varNumeroDocumento' => 'STR',
				'mvi_decCantidadTotal' => 'STR',
				'mvi_decBruto' => 'STR',
				'mvi_decDescuento' => 'STR',
				'mvi_decIva' => 'STR',
				'mvi_decIca' => 'STR',
				'mvi_decRetencion' => 'STR',
				'mvi_decReteIva' => 'STR',
				'mvi_dateFecha' => 'STR',
				'mvi_decNeto' => 'STR',
				'mvi_dateFechaVencimiento' => 'STR',
				'mvi_decPorceRetefuente' => 'STR',
				'mvi_intTopeRetefuente' => 'INT',
				'mvi_decPorceReteiva' => 'STR',
				'mvi_intTopeReteiva' => 'INT',
				'mvi_decPorceIca' => 'STR',
				'mvi_intTopeReteIca' => 'INT',
				'mvi_dtimFechaCreacion' => 'STR',
				'mvi_dtimFechaActualizacion' => 'STR',
				'mvi_varNota' => 'STR',
				'est_intIdEstado' => 'INT',
				'cen_intIdCentrocostos' => 'INT',
				'doc_intDocumento' => 'INT',
				'cse_intIdConsecutivo' => 'INT',
				'per_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['mvi_intId'],

			'autoincrement' => 'mvi_intId',

			'nullable'		=> ['mvi_intId', 'mvi_varNumeroDocumento', 'mvi_decPorceIca', 'mvi_intTopeReteIca', 'mvi_dtimFechaCreacion', 'mvi_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'mvi_intId' => ['type' => 'int'],
				'mvi_varNumeroDocumento' => ['type' => 'str', 'max' => 20],
				'mvi_decCantidadTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvi_decBruto' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvi_decDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvi_decIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvi_decIca' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvi_decRetencion' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvi_decReteIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvi_dateFecha' => ['type' => 'date', 'required' => true],
				'mvi_decNeto' => ['type' => 'decimal(18,2)', 'required' => true],
				'mvi_dateFechaVencimiento' => ['type' => 'date', 'required' => true],
				'mvi_decPorceRetefuente' => ['type' => 'decimal(10,2)', 'required' => true],
				'mvi_intTopeRetefuente' => ['type' => 'int', 'required' => true],
				'mvi_decPorceReteiva' => ['type' => 'decimal(10,2)', 'required' => true],
				'mvi_intTopeReteiva' => ['type' => 'int', 'required' => true],
				'mvi_decPorceIca' => ['type' => 'decimal(10,2)'],
				'mvi_intTopeReteIca' => ['type' => 'int'],
				'mvi_dtimFechaCreacion' => ['type' => 'datetime'],
				'mvi_dtimFechaActualizacion' => ['type' => 'datetime'],
				'mvi_varNota' => ['type' => 'str', 'max' => 255, 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'cen_intIdCentrocostos' => ['type' => 'int', 'required' => true],
				'doc_intDocumento' => ['type' => 'int', 'required' => true],
				'cse_intIdConsecutivo' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cen_intIdCentrocostos', 'cse_intIdConsecutivo', 'doc_intDocumento', 'est_intIdEstado', 'per_intIdPersona', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_mvto_inventario.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_mvto_inventario.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_mvto_inventario.doc_intDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_mvto_inventario.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_mvto_inventario.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_mvto_inventario.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_mvto_inventario.usu_intIdCreador']
				],
				'tbl_mvto_inventario_detalle' => [
					['tbl_mvto_inventario_detalle.mvi_intIdMvtoInventario','tbl_mvto_inventario.mvi_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_centro_costos' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_mvto_inventario',
				        1 => 'cen_intIdCentrocostos',
				      ),
				    ),
				  ),
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
				        0 => 'tbl_mvto_inventario',
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
				        0 => 'tbl_mvto_inventario',
				        1 => 'doc_intDocumento',
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
				        0 => 'tbl_mvto_inventario',
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
				        0 => 'tbl_mvto_inventario',
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
				        0 => 'tbl_mvto_inventario',
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
				        0 => 'tbl_mvto_inventario',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_mvto_inventario_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_mvto_inventario_detalle',
				        1 => 'mvi_intIdMvtoInventario',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_mvto_inventario',
				        1 => 'mvi_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_mvto_inventario.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_mvto_inventario.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_mvto_inventario.doc_intDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_mvto_inventario.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_mvto_inventario.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_mvto_inventario.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_mvto_inventario.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_centro_costos' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_centro_costos',
				        1 => 'cco_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_mvto_inventario',
				        1 => 'cen_intIdCentrocostos',
				      ),
				    ),
				  ),
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
				        0 => 'tbl_mvto_inventario',
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
				        0 => 'tbl_mvto_inventario',
				        1 => 'doc_intDocumento',
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
				        0 => 'tbl_mvto_inventario',
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
				        0 => 'tbl_mvto_inventario',
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
				        0 => 'tbl_mvto_inventario',
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
				        0 => 'tbl_mvto_inventario',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


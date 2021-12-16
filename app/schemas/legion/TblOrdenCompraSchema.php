<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblOrdenCompraSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_orden_compra',

			'id_name'		=> 'oco_intId',

			'attr_types'	=> [
				'oco_intId' => 'INT',
				'oco_varNumeroDocumento' => 'STR',
				'oco_decCantidadTotal' => 'STR',
				'oco_decBruto' => 'STR',
				'oco_decDescuento' => 'STR',
				'oco_decIva' => 'STR',
				'oco_decIca' => 'STR',
				'oco_decRetencionfuente' => 'STR',
				'oco_decReteIva' => 'STR',
				'oco_dateFecha' => 'STR',
				'oco_decNeto' => 'STR',
				'oco_decPorceRetefuente' => 'STR',
				'oco_intTopeRetefuente' => 'INT',
				'oco_decPorceReteiva' => 'STR',
				'oco_intTopeReteiva' => 'INT',
				'oco_decPorceIca' => 'STR',
				'oco_intTopeReteIca' => 'INT',
				'oco_bolEstado' => 'INT',
				'oco_bolNotificacion' => 'INT',
				'oco_dtimFechaCreacion' => 'STR',
				'oco_dtimFechaActualizacion' => 'STR',
				'oco_varNota' => 'STR',
				'cen_intIdCentrocostos' => 'INT',
				'doc_intDocumento' => 'INT',
				'cse_intIdConsecutivo' => 'INT',
				'per_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['oco_intId'],

			'autoincrement' => 'oco_intId',

			'nullable'		=> ['oco_intId', 'oco_decIva', 'oco_decIca', 'oco_decRetencionfuente', 'oco_decReteIva', 'oco_dateFecha', 'oco_decPorceRetefuente', 'oco_intTopeRetefuente', 'oco_decPorceReteiva', 'oco_intTopeReteiva', 'oco_intTopeReteIca', 'oco_bolEstado', 'oco_bolNotificacion', 'oco_dtimFechaCreacion', 'oco_dtimFechaActualizacion', 'oco_varNota', 'cen_intIdCentrocostos', 'doc_intDocumento', 'cse_intIdConsecutivo', 'per_intIdPersona', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'oco_intId' => ['type' => 'int'],
				'oco_varNumeroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'oco_decCantidadTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'oco_decBruto' => ['type' => 'decimal(18,2)', 'required' => true],
				'oco_decDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'oco_decIva' => ['type' => 'decimal(18,2)'],
				'oco_decIca' => ['type' => 'decimal(18,2)'],
				'oco_decRetencionfuente' => ['type' => 'decimal(18,2)'],
				'oco_decReteIva' => ['type' => 'decimal(18,2)'],
				'oco_dateFecha' => ['type' => 'date'],
				'oco_decNeto' => ['type' => 'decimal(18,2)', 'required' => true],
				'oco_decPorceRetefuente' => ['type' => 'decimal(10,2)'],
				'oco_intTopeRetefuente' => ['type' => 'int'],
				'oco_decPorceReteiva' => ['type' => 'decimal(10,2)'],
				'oco_intTopeReteiva' => ['type' => 'int'],
				'oco_decPorceIca' => ['type' => 'decimal(10,2)', 'required' => true],
				'oco_intTopeReteIca' => ['type' => 'int'],
				'oco_bolEstado' => ['type' => 'bool'],
				'oco_bolNotificacion' => ['type' => 'bool'],
				'oco_dtimFechaCreacion' => ['type' => 'datetime'],
				'oco_dtimFechaActualizacion' => ['type' => 'datetime'],
				'oco_varNota' => ['type' => 'str', 'max' => 250],
				'cen_intIdCentrocostos' => ['type' => 'int'],
				'doc_intDocumento' => ['type' => 'int'],
				'cse_intIdConsecutivo' => ['type' => 'int'],
				'per_intIdPersona' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cen_intIdCentrocostos', 'cse_intIdConsecutivo', 'doc_intDocumento', 'per_intIdPersona', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_orden_compra.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_orden_compra.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_orden_compra.doc_intDocumento']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_orden_compra.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_orden_compra.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_orden_compra.usu_intIdCreador']
				],
				'tbl_orden_compra_detalle' => [
					['tbl_orden_compra_detalle.oco_intIdordenCompra','tbl_orden_compra.oco_intId']
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
				        0 => 'tbl_orden_compra',
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
				        0 => 'tbl_orden_compra',
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
				        0 => 'tbl_orden_compra',
				        1 => 'doc_intDocumento',
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
				        0 => 'tbl_orden_compra',
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
				        0 => 'tbl_orden_compra',
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
				        0 => 'tbl_orden_compra',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_orden_compra_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_orden_compra_detalle',
				        1 => 'oco_intIdordenCompra',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_orden_compra',
				        1 => 'oco_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_orden_compra.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_orden_compra.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_orden_compra.doc_intDocumento']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_orden_compra.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_orden_compra.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_orden_compra.usu_intIdCreador']
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
				        0 => 'tbl_orden_compra',
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
				        0 => 'tbl_orden_compra',
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
				        0 => 'tbl_orden_compra',
				        1 => 'doc_intDocumento',
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
				        0 => 'tbl_orden_compra',
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
				        0 => 'tbl_orden_compra',
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
				        0 => 'tbl_orden_compra',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblFacturaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_factura',

			'id_name'		=> 'fac_intId',

			'attr_types'	=> [
				'fac_intId' => 'INT',
				'fac_varNroDocumento' => 'STR',
				'fac_decCantidadTotal' => 'STR',
				'fac_decBruto' => 'STR',
				'fac_decDescuento' => 'STR',
				'fac_decIva' => 'STR',
				'fac_decIca' => 'STR',
				'fac_decRetencion' => 'STR',
				'fac_decReteIva' => 'STR',
				'fac_dateFecha' => 'STR',
				'fac_decNeto' => 'STR',
				'fac_bolEstado' => 'INT',
				'fac_dateFechaVencimiento' => 'STR',
				'fac_decPorceRetefuente' => 'STR',
				'fac_intTopeRetefuente' => 'INT',
				'fac_decPorceReteiva' => 'STR',
				'fac_intTopeReteiva' => 'INT',
				'fac_decPorceIca' => 'STR',
				'fac_intTopeReteIca' => 'INT',
				'fac_dtimFechaCreacion' => 'STR',
				'fac_dtimFechaActualizacion' => 'STR',
				'fac_varNota' => 'STR',
				'fac_bolPagado' => 'INT',
				'fac_bolEnviadoDian' => 'INT',
				'fac_bolNotaCredito' => 'INT',
				'fac_bolNotaDebito' => 'INT',
				'fac_lonCUFEDian' => 'STR',
				'fac_dateFechaEmision' => 'STR',
				'tmp_intIdMedioPago' => 'INT',
				'tdp_intIdFormaPago' => 'INT',
				'cen_intIdCentrocostos' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'cse_intIdConsecutivo' => 'INT',
				'per_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['fac_intId'],

			'autoincrement' => 'fac_intId',

			'nullable'		=> ['fac_intId', 'fac_varNroDocumento', 'fac_bolEstado', 'fac_dtimFechaCreacion', 'fac_dtimFechaActualizacion', 'fac_bolPagado', 'fac_bolEnviadoDian', 'fac_bolNotaCredito', 'fac_bolNotaDebito', 'fac_lonCUFEDian', 'fac_dateFechaEmision', 'tmp_intIdMedioPago', 'tdp_intIdFormaPago'],

			'uniques'		=> [],

			'rules' 		=> [
				'fac_intId' => ['type' => 'int'],
				'fac_varNroDocumento' => ['type' => 'str', 'max' => 20],
				'fac_decCantidadTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'fac_decBruto' => ['type' => 'decimal(18,2)', 'required' => true],
				'fac_decDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'fac_decIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'fac_decIca' => ['type' => 'decimal(18,2)', 'required' => true],
				'fac_decRetencion' => ['type' => 'decimal(18,2)', 'required' => true],
				'fac_decReteIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'fac_dateFecha' => ['type' => 'date', 'required' => true],
				'fac_decNeto' => ['type' => 'decimal(18,2)', 'required' => true],
				'fac_bolEstado' => ['type' => 'bool'],
				'fac_dateFechaVencimiento' => ['type' => 'date', 'required' => true],
				'fac_decPorceRetefuente' => ['type' => 'decimal(10,2)', 'required' => true],
				'fac_intTopeRetefuente' => ['type' => 'int', 'required' => true],
				'fac_decPorceReteiva' => ['type' => 'decimal(10,2)', 'required' => true],
				'fac_intTopeReteiva' => ['type' => 'int', 'required' => true],
				'fac_decPorceIca' => ['type' => 'decimal(10,2)', 'required' => true],
				'fac_intTopeReteIca' => ['type' => 'int', 'required' => true],
				'fac_dtimFechaCreacion' => ['type' => 'datetime'],
				'fac_dtimFechaActualizacion' => ['type' => 'datetime'],
				'fac_varNota' => ['type' => 'str', 'required' => true],
				'fac_bolPagado' => ['type' => 'bool'],
				'fac_bolEnviadoDian' => ['type' => 'bool'],
				'fac_bolNotaCredito' => ['type' => 'bool'],
				'fac_bolNotaDebito' => ['type' => 'bool'],
				'fac_lonCUFEDian' => ['type' => 'str'],
				'fac_dateFechaEmision' => ['type' => 'datetime'],
				'tmp_intIdMedioPago' => ['type' => 'int'],
				'tdp_intIdFormaPago' => ['type' => 'int'],
				'cen_intIdCentrocostos' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'cse_intIdConsecutivo' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['cen_intIdCentrocostos', 'cse_intIdConsecutivo', 'doc_intIdDocumento', 'tdp_intIdFormaPago', 'per_intIdPersona', 'tmp_intIdMedioPago', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_factura.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_factura.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_factura.doc_intIdDocumento']
				],
				'tbl_forma_de_pago' => [
					['tbl_forma_de_pago.fdp_intId','tbl_factura.tdp_intIdFormaPago']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_factura.per_intIdPersona']
				],
				'tbl_medio_pago' => [
					['tbl_medio_pago.tmp_intId','tbl_factura.tmp_intIdMedioPago']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_factura.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_factura.usu_intIdCreador']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.fac_intIdFactura','tbl_factura.fac_intId']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.fac_intIdFactura','tbl_factura.fac_intId']
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
				        0 => 'tbl_factura',
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
				        0 => 'tbl_factura',
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
				        0 => 'tbl_factura',
				        1 => 'doc_intIdDocumento',
				      ),
				    ),
				  ),
				  'tbl_forma_de_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_forma_de_pago',
				        1 => 'fdp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'tdp_intIdFormaPago',
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
				        0 => 'tbl_factura',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_medio_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_medio_pago',
				        1 => 'tmp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'tmp_intIdMedioPago',
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
				        0 => 'tbl_factura',
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
				        0 => 'tbl_factura',
				        1 => 'usu_intIdCreador',
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
				        1 => 'fac_intIdFactura',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'fac_intId',
				      ),
				    ),
				  ),
				  'tbl_factura_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_factura_detalle',
				        1 => 'fac_intIdFactura',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'fac_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_factura.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_factura.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_factura.doc_intIdDocumento']
				],
				'tbl_forma_de_pago' => [
					['tbl_forma_de_pago.fdp_intId','tbl_factura.tdp_intIdFormaPago']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_factura.per_intIdPersona']
				],
				'tbl_medio_pago' => [
					['tbl_medio_pago.tmp_intId','tbl_factura.tmp_intIdMedioPago']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_factura.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_factura.usu_intIdCreador']
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
				        0 => 'tbl_factura',
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
				        0 => 'tbl_factura',
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
				        0 => 'tbl_factura',
				        1 => 'doc_intIdDocumento',
				      ),
				    ),
				  ),
				  'tbl_forma_de_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_forma_de_pago',
				        1 => 'fdp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'tdp_intIdFormaPago',
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
				        0 => 'tbl_factura',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_medio_pago' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_medio_pago',
				        1 => 'tmp_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'tmp_intIdMedioPago',
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
				        0 => 'tbl_factura',
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
				        0 => 'tbl_factura',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


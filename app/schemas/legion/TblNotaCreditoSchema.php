<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblNotaCreditoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_nota_credito',

			'id_name'		=> 'nct_intId',

			'attr_types'	=> [
				'nct_intId' => 'INT',
				'nct_varNroDocumento' => 'STR',
				'nct_decCantidadTotal' => 'STR',
				'nct_decBruto' => 'STR',
				'nct_decDescuento' => 'STR',
				'nct_decIva' => 'STR',
				'nct_decIca' => 'STR',
				'nct_decRetencion' => 'STR',
				'nct_decReteIva' => 'STR',
				'nct_dateFecha' => 'STR',
				'nct_decNeto' => 'STR',
				'nct_decPorceRetefuente' => 'STR',
				'nct_intTopeRetefuente' => 'INT',
				'nct_decPorceReteiva' => 'STR',
				'nct_intTopeReteiva' => 'INT',
				'nct_decPorceIca' => 'STR',
				'nct_intTopeReteIca' => 'INT',
				'nct_dtimFechaCreacion' => 'STR',
				'nct_dtimFechaActualizacion' => 'STR',
				'nct_varNota' => 'STR',
				'nct_bolCruzado' => 'INT',
				'nct_bolEnviadoDian' => 'INT',
				'fac_varNroDocumento' => 'STR',
				'fac_intIdFactura' => 'INT',
				'cen_intIdCentrocostos' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'cse_intIdConsecutivo' => 'INT',
				'per_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['nct_intId'],

			'autoincrement' => 'nct_intId',

			'nullable'		=> ['nct_intId', 'nct_varNroDocumento', 'nct_dateFecha', 'nct_dtimFechaCreacion', 'nct_dtimFechaActualizacion', 'nct_varNota', 'nct_bolCruzado', 'nct_bolEnviadoDian', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'nct_intId' => ['type' => 'int'],
				'nct_varNroDocumento' => ['type' => 'str', 'max' => 20],
				'nct_decCantidadTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'nct_decBruto' => ['type' => 'decimal(18,2)', 'required' => true],
				'nct_decDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'nct_decIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'nct_decIca' => ['type' => 'decimal(18,2)', 'required' => true],
				'nct_decRetencion' => ['type' => 'decimal(18,2)', 'required' => true],
				'nct_decReteIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'nct_dateFecha' => ['type' => 'date'],
				'nct_decNeto' => ['type' => 'decimal(18,2)', 'required' => true],
				'nct_decPorceRetefuente' => ['type' => 'decimal(10,2)', 'required' => true],
				'nct_intTopeRetefuente' => ['type' => 'int', 'required' => true],
				'nct_decPorceReteiva' => ['type' => 'decimal(10,2)', 'required' => true],
				'nct_intTopeReteiva' => ['type' => 'int', 'required' => true],
				'nct_decPorceIca' => ['type' => 'decimal(10,2)', 'required' => true],
				'nct_intTopeReteIca' => ['type' => 'int', 'required' => true],
				'nct_dtimFechaCreacion' => ['type' => 'datetime'],
				'nct_dtimFechaActualizacion' => ['type' => 'datetime'],
				'nct_varNota' => ['type' => 'str', 'max' => 250],
				'nct_bolCruzado' => ['type' => 'bool'],
				'nct_bolEnviadoDian' => ['type' => 'bool'],
				'fac_varNroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'fac_intIdFactura' => ['type' => 'int', 'required' => true],
				'cen_intIdCentrocostos' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'cse_intIdConsecutivo' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cen_intIdCentrocostos', 'cse_intIdConsecutivo', 'doc_intIdDocumento', 'fac_intIdFactura', 'per_intIdPersona', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_nota_credito.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_nota_credito.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_nota_credito.doc_intIdDocumento']
				],
				'tbl_factura' => [
					['tbl_factura.fac_intId','tbl_nota_credito.fac_intIdFactura']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_nota_credito.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_nota_credito.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_nota_credito.usu_intIdCreador']
				],
				'tbl_nota_debito' => [
					['tbl_nota_debito.nct_intIdNotaCredito','tbl_nota_credito.nct_intId']
				],
				'tbl_nota_credito_detalle' => [
					['tbl_nota_credito_detalle.nct_intIdNotaCredito','tbl_nota_credito.nct_intId']
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
				        0 => 'tbl_nota_credito',
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
				        0 => 'tbl_nota_credito',
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
				        0 => 'tbl_nota_credito',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'fac_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_credito',
				        1 => 'fac_intIdFactura',
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
				        0 => 'tbl_nota_credito',
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
				        0 => 'tbl_nota_credito',
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
				        0 => 'tbl_nota_credito',
				        1 => 'usu_intIdCreador',
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
				        1 => 'nct_intIdNotaCredito',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_credito',
				        1 => 'nct_intId',
				      ),
				    ),
				  ),
				  'tbl_nota_credito_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_credito_detalle',
				        1 => 'nct_intIdNotaCredito',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_credito',
				        1 => 'nct_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_nota_credito.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_nota_credito.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_nota_credito.doc_intIdDocumento']
				],
				'tbl_factura' => [
					['tbl_factura.fac_intId','tbl_nota_credito.fac_intIdFactura']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_nota_credito.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_nota_credito.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_nota_credito.usu_intIdCreador']
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
				        0 => 'tbl_nota_credito',
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
				        0 => 'tbl_nota_credito',
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
				        0 => 'tbl_nota_credito',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'fac_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_credito',
				        1 => 'fac_intIdFactura',
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
				        0 => 'tbl_nota_credito',
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
				        0 => 'tbl_nota_credito',
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
				        0 => 'tbl_nota_credito',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


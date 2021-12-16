<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblNotaDebitoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_nota_debito',

			'id_name'		=> 'nbt_intId',

			'attr_types'	=> [
				'nbt_intId' => 'INT',
				'nbt_varNroDocumento' => 'STR',
				'nbt_decCantidadTotal' => 'STR',
				'nbt_decBruto' => 'STR',
				'nbt_decDescuento' => 'STR',
				'nbt_decIva' => 'STR',
				'nbt_decIca' => 'STR',
				'nbt_decRetencion' => 'STR',
				'nbt_decReteIva' => 'STR',
				'nbt_dateFecha' => 'STR',
				'nbt_decNeto' => 'STR',
				'nbt_decPorceRetefuente' => 'STR',
				'nbt_intTopeRetefuente' => 'INT',
				'nbt_decPorceReteiva' => 'STR',
				'nbt_intTopeReteiva' => 'INT',
				'nbt_decPorceIca' => 'STR',
				'nbt_intTopeReteIca' => 'INT',
				'nbt_dtimFechaCreacion' => 'STR',
				'nbt_dtimFechaActualizacion' => 'STR',
				'nbt_lonNota' => 'STR',
				'nbt_bolCruzado' => 'INT',
				'nct_varNroDocumento' => 'STR',
				'nbt_bolEnviadoDian' => 'INT',
				'nct_intIdNotaCredito' => 'INT',
				'cen_intIdCentrocostos' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'cse_intIdConsecutivo' => 'INT',
				'per_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['nbt_intId'],

			'autoincrement' => 'nbt_intId',

			'nullable'		=> ['nbt_intId', 'nbt_dtimFechaCreacion', 'nbt_dtimFechaActualizacion', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'nbt_intId' => ['type' => 'int'],
				'nbt_varNroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'nbt_decCantidadTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_decBruto' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_decDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_decIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_decIca' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_decRetencion' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_decReteIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_dateFecha' => ['type' => 'date', 'required' => true],
				'nbt_decNeto' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_decPorceRetefuente' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_intTopeRetefuente' => ['type' => 'int', 'required' => true],
				'nbt_decPorceReteiva' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_intTopeReteiva' => ['type' => 'int', 'required' => true],
				'nbt_decPorceIca' => ['type' => 'decimal(18,2)', 'required' => true],
				'nbt_intTopeReteIca' => ['type' => 'int', 'required' => true],
				'nbt_dtimFechaCreacion' => ['type' => 'datetime'],
				'nbt_dtimFechaActualizacion' => ['type' => 'datetime'],
				'nbt_lonNota' => ['type' => 'str', 'required' => true],
				'nbt_bolCruzado' => ['type' => 'bool', 'required' => true],
				'nct_varNroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'nbt_bolEnviadoDian' => ['type' => 'bool', 'required' => true],
				'nct_intIdNotaCredito' => ['type' => 'int', 'required' => true],
				'cen_intIdCentrocostos' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'cse_intIdConsecutivo' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cen_intIdCentrocostos', 'cse_intIdConsecutivo', 'doc_intIdDocumento', 'nct_intIdNotaCredito', 'per_intIdPersona', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_nota_debito.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_nota_debito.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_nota_debito.doc_intIdDocumento']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.nct_intId','tbl_nota_debito.nct_intIdNotaCredito']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_nota_debito.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_nota_debito.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_nota_debito.usu_intIdActualizador']
				],
				'tbl_nota_debito_detalle' => [
					['tbl_nota_debito_detalle.nbt_intIdNotadebito','tbl_nota_debito.nbt_intId']
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
				        0 => 'tbl_nota_debito',
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
				        0 => 'tbl_nota_debito',
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
				        0 => 'tbl_nota_debito',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'nct_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_debito',
				        1 => 'nct_intIdNotaCredito',
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
				        0 => 'tbl_nota_debito',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_debito',
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
				        0 => 'tbl_nota_debito',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				  'tbl_nota_debito_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_nota_debito_detalle',
				        1 => 'nbt_intIdNotadebito',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_debito',
				        1 => 'nbt_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_nota_debito.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_nota_debito.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_nota_debito.doc_intIdDocumento']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.nct_intId','tbl_nota_debito.nct_intIdNotaCredito']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_nota_debito.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_nota_debito.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_nota_debito.usu_intIdActualizador']
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
				        0 => 'tbl_nota_debito',
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
				        0 => 'tbl_nota_debito',
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
				        0 => 'tbl_nota_debito',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'nct_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_debito',
				        1 => 'nct_intIdNotaCredito',
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
				        0 => 'tbl_nota_debito',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_debito',
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
				        0 => 'tbl_nota_debito',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


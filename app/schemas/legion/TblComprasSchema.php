<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblComprasSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_compras',

			'id_name'		=> 'com_intId',

			'attr_types'	=> [
				'com_intId' => 'INT',
				'com_varNumeroDocumento' => 'STR',
				'com_decCantidadTotal' => 'STR',
				'com_decBruto' => 'STR',
				'com_decDescuento' => 'STR',
				'com_decIva' => 'STR',
				'com_decIca' => 'STR',
				'com_decRetencion' => 'STR',
				'com_decReteIva' => 'STR',
				'com_dateFecha' => 'STR',
				'com_decNeto' => 'STR',
				'com_bolEstado' => 'INT',
				'com_dateFechaVencimiento' => 'STR',
				'com_decPorceRetefuente' => 'STR',
				'com_intTopeRetefuente' => 'INT',
				'com_decPorceReteiva' => 'STR',
				'com_intTopeReteiva' => 'INT',
				'com_decPorceIca' => 'STR',
				'com_intTopeReteIca' => 'INT',
				'com_dtimFechaCreacion' => 'STR',
				'com_dtimFechaActualizacion' => 'STR',
				'com_varNota' => 'STR',
				'com_varFacturaProveedor' => 'STR',
				'est_intidEstado' => 'INT',
				'doc_intDocumento' => 'INT',
				'cse_intIdConsecutivo' => 'INT',
				'per_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['com_intId'],

			'autoincrement' => 'com_intId',

			'nullable'		=> ['com_intId', 'com_dateFecha', 'com_decNeto', 'com_bolEstado', 'com_dtimFechaCreacion', 'com_dtimFechaActualizacion', 'com_varNota', 'com_varFacturaProveedor', 'est_intidEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'com_intId' => ['type' => 'int'],
				'com_varNumeroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'com_decCantidadTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'com_decBruto' => ['type' => 'decimal(18,2)', 'required' => true],
				'com_decDescuento' => ['type' => 'decimal(18,2)', 'required' => true],
				'com_decIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'com_decIca' => ['type' => 'decimal(18,2)', 'required' => true],
				'com_decRetencion' => ['type' => 'decimal(18,2)', 'required' => true],
				'com_decReteIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'com_dateFecha' => ['type' => 'date'],
				'com_decNeto' => ['type' => 'decimal(18,2)'],
				'com_bolEstado' => ['type' => 'bool'],
				'com_dateFechaVencimiento' => ['type' => 'date', 'required' => true],
				'com_decPorceRetefuente' => ['type' => 'decimal(10,2)', 'required' => true],
				'com_intTopeRetefuente' => ['type' => 'int', 'required' => true],
				'com_decPorceReteiva' => ['type' => 'decimal(10,2)', 'required' => true],
				'com_intTopeReteiva' => ['type' => 'int', 'required' => true],
				'com_decPorceIca' => ['type' => 'decimal(10,2)', 'required' => true],
				'com_intTopeReteIca' => ['type' => 'int', 'required' => true],
				'com_dtimFechaCreacion' => ['type' => 'datetime'],
				'com_dtimFechaActualizacion' => ['type' => 'datetime'],
				'com_varNota' => ['type' => 'str'],
				'com_varFacturaProveedor' => ['type' => 'str', 'max' => 100],
				'est_intidEstado' => ['type' => 'int'],
				'doc_intDocumento' => ['type' => 'int', 'required' => true],
				'cse_intIdConsecutivo' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cse_intIdConsecutivo', 'doc_intDocumento', 'est_intidEstado', 'per_intIdPersona', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_compras.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_compras.doc_intDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_compras.est_intidEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_compras.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_compras.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_compras.usu_intIdActualizador']
				],
				'tbl_compras_detalle' => [
					['tbl_compras_detalle.com_intIdCompras','tbl_compras.com_intId']
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
				        0 => 'tbl_compras',
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
				        0 => 'tbl_compras',
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
				        0 => 'tbl_compras',
				        1 => 'est_intidEstado',
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
				        0 => 'tbl_compras',
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
				        0 => 'tbl_compras',
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
				        0 => 'tbl_compras',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				  'tbl_compras_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_compras_detalle',
				        1 => 'com_intIdCompras',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_compras',
				        1 => 'com_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_compras.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_compras.doc_intDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_compras.est_intidEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_compras.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_compras.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_compras.usu_intIdActualizador']
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
				        0 => 'tbl_compras',
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
				        0 => 'tbl_compras',
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
				        0 => 'tbl_compras',
				        1 => 'est_intidEstado',
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
				        0 => 'tbl_compras',
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
				        0 => 'tbl_compras',
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
				        0 => 'tbl_compras',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


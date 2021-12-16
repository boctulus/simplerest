<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblNotaCreditoDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_nota_credito_detalle',

			'id_name'		=> 'ncd_intId',

			'attr_types'	=> [
				'ncd_intId' => 'INT',
				'ncd_datFecha' => 'STR',
				'ncd_decValor' => 'STR',
				'ncd_decCantidad' => 'STR',
				'ncd_decValorTotal' => 'STR',
				'ncd_decPorcentajeIva' => 'STR',
				'ncd_decValorIva' => 'STR',
				'ncd_lonDescripcion' => 'STR',
				'ncd_dtimFechaCreacion' => 'STR',
				'ncd_dtimFechaActualizacion' => 'STR',
				'nct_varNroDocumento' => 'STR',
				'nct_intIdNotaCredito' => 'INT',
				'per_intIdPersona' => 'INT',
				'pro_intIdProducto' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ncd_intId'],

			'autoincrement' => 'ncd_intId',

			'nullable'		=> ['ncd_intId', 'ncd_dtimFechaCreacion', 'ncd_dtimFechaActualizacion', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'ncd_intId' => ['type' => 'int'],
				'ncd_datFecha' => ['type' => 'date', 'required' => true],
				'ncd_decValor' => ['type' => 'decimal(18,2)', 'required' => true],
				'ncd_decCantidad' => ['type' => 'decimal(18,2)', 'required' => true],
				'ncd_decValorTotal' => ['type' => 'decimal(18,2)', 'required' => true],
				'ncd_decPorcentajeIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'ncd_decValorIva' => ['type' => 'decimal(18,2)', 'required' => true],
				'ncd_lonDescripcion' => ['type' => 'str', 'required' => true],
				'ncd_dtimFechaCreacion' => ['type' => 'datetime'],
				'ncd_dtimFechaActualizacion' => ['type' => 'datetime'],
				'nct_varNroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'nct_intIdNotaCredito' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'pro_intIdProducto' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['doc_intIdDocumento', 'nct_intIdNotaCredito', 'per_intIdPersona', 'pro_intIdProducto', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_nota_credito_detalle.doc_intIdDocumento']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.nct_intId','tbl_nota_credito_detalle.nct_intIdNotaCredito']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_nota_credito_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_nota_credito_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_nota_credito_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_nota_credito_detalle.usu_intIdActualizador']
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
				        0 => 'tbl_nota_credito_detalle',
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
				        0 => 'tbl_nota_credito_detalle',
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
				        0 => 'tbl_nota_credito_detalle',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'pro_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_credito_detalle',
				        1 => 'pro_intIdProducto',
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
				        0 => 'tbl_nota_credito_detalle',
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
				        0 => 'tbl_nota_credito_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_nota_credito_detalle.doc_intIdDocumento']
				],
				'tbl_nota_credito' => [
					['tbl_nota_credito.nct_intId','tbl_nota_credito_detalle.nct_intIdNotaCredito']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_nota_credito_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_nota_credito_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_nota_credito_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_nota_credito_detalle.usu_intIdActualizador']
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
				        0 => 'tbl_nota_credito_detalle',
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
				        0 => 'tbl_nota_credito_detalle',
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
				        0 => 'tbl_nota_credito_detalle',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'pro_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_credito_detalle',
				        1 => 'pro_intIdProducto',
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
				        0 => 'tbl_nota_credito_detalle',
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
				        0 => 'tbl_nota_credito_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


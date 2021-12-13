<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblNotaDebitoDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_nota_debito_detalle',

			'id_name'		=> 'ndt_intId',

			'attr_types'	=> [
				'ndt_intId' => 'INT',
				'ndt_dateFecha' => 'STR',
				'ndt_decValor' => 'STR',
				'ndt_bolEstado' => 'INT',
				'ndt_decCantidad' => 'STR',
				'ndt_decValorTotal' => 'STR',
				'ndt_decPorcentajeIva' => 'STR',
				'ndt_decValorIva' => 'STR',
				'ndt_dtimFechaCreacion' => 'STR',
				'ndt_dtimFechaActualizacion' => 'STR',
				'ndt_intNroDocumento' => 'STR',
				'ndt_varDescripcion' => 'STR',
				'ndt_intIdNotadebito' => 'INT',
				'per_intIdPersona' => 'INT',
				'pro_intIdProducto' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ndt_intId'],

			'autoincrement' => 'ndt_intId',

			'nullable'		=> ['ndt_intId', 'ndt_decValor', 'ndt_bolEstado', 'ndt_decCantidad', 'ndt_decValorTotal', 'ndt_decPorcentajeIva', 'ndt_decValorIva', 'ndt_dtimFechaCreacion', 'ndt_dtimFechaActualizacion', 'ndt_varDescripcion', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'ndt_intId' => ['type' => 'int'],
				'ndt_dateFecha' => ['type' => 'date', 'required' => true],
				'ndt_decValor' => ['type' => 'decimal(18,2)'],
				'ndt_bolEstado' => ['type' => 'bool'],
				'ndt_decCantidad' => ['type' => 'decimal(18,2)'],
				'ndt_decValorTotal' => ['type' => 'decimal(18,2)'],
				'ndt_decPorcentajeIva' => ['type' => 'decimal(18,2)'],
				'ndt_decValorIva' => ['type' => 'decimal(18,2)'],
				'ndt_dtimFechaCreacion' => ['type' => 'datetime'],
				'ndt_dtimFechaActualizacion' => ['type' => 'datetime'],
				'ndt_intNroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'ndt_varDescripcion' => ['type' => 'str', 'max' => 100],
				'ndt_intIdNotadebito' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'pro_intIdProducto' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['doc_intIdDocumento', 'ndt_intIdNotadebito', 'per_intIdPersona', 'pro_intIdProducto', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_nota_debito_detalle.doc_intIdDocumento']
				],
				'tbl_nota_debito' => [
					['tbl_nota_debito.nbt_intId','tbl_nota_debito_detalle.ndt_intIdNotadebito']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_nota_debito_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_nota_debito_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_nota_debito_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_nota_debito_detalle.usu_intIdActualizador']
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
				        0 => 'tbl_nota_debito_detalle',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'nbt_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_debito_detalle',
				        1 => 'ndt_intIdNotadebito',
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
				        0 => 'tbl_nota_debito_detalle',
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
				        0 => 'tbl_nota_debito_detalle',
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
				        0 => 'tbl_nota_debito_detalle',
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
				        0 => 'tbl_nota_debito_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_nota_debito_detalle.doc_intIdDocumento']
				],
				'tbl_nota_debito' => [
					['tbl_nota_debito.nbt_intId','tbl_nota_debito_detalle.ndt_intIdNotadebito']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_nota_debito_detalle.per_intIdPersona']
				],
				'tbl_producto' => [
					['tbl_producto.pro_intId','tbl_nota_debito_detalle.pro_intIdProducto']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_nota_debito_detalle.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_nota_debito_detalle.usu_intIdActualizador']
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
				        0 => 'tbl_nota_debito_detalle',
				        1 => 'doc_intIdDocumento',
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
				        1 => 'nbt_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_nota_debito_detalle',
				        1 => 'ndt_intIdNotadebito',
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
				        0 => 'tbl_nota_debito_detalle',
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
				        0 => 'tbl_nota_debito_detalle',
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
				        0 => 'tbl_nota_debito_detalle',
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
				        0 => 'tbl_nota_debito_detalle',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


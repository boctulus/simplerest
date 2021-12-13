<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPedidoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_pedido',

			'id_name'		=> 'ecp_intId',

			'attr_types'	=> [
				'ecp_intId' => 'INT',
				'ecp_varNroDocumento' => 'STR',
				'ecp_decCantidadTotal' => 'STR',
				'ecp_decDescuento' => 'STR',
				'ecp_decValorbruto' => 'STR',
				'ecp_decIva' => 'STR',
				'ecp_decRetefuente' => 'STR',
				'ecp_decReteIca' => 'STR',
				'ecp_decReteIva' => 'STR',
				'ecp_decValorNeto' => 'STR',
				'ecp_datFechaEmision' => 'STR',
				'ecp_datFechaVencimiento' => 'STR',
				'ecp_lonNota' => 'STR',
				'ecp_dtimFechaCreacion' => 'STR',
				'ecp_dtimFechaActualizacion' => 'STR',
				'per_intIdPersona' => 'INT',
				'doc_intIdDocumento' => 'INT',
				'cen_intIdCentrocostos' => 'INT',
				'est_intEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ecp_intId'],

			'autoincrement' => 'ecp_intId',

			'nullable'		=> ['ecp_intId', 'ecp_varNroDocumento', 'ecp_dtimFechaCreacion', 'ecp_dtimFechaActualizacion', 'est_intEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'ecp_intId' => ['type' => 'int'],
				'ecp_varNroDocumento' => ['type' => 'str', 'max' => 20],
				'ecp_decCantidadTotal' => ['type' => 'decimal(18,4)', 'required' => true],
				'ecp_decDescuento' => ['type' => 'decimal(18,4)', 'required' => true],
				'ecp_decValorbruto' => ['type' => 'decimal(18,4)', 'required' => true],
				'ecp_decIva' => ['type' => 'decimal(18,4)', 'required' => true],
				'ecp_decRetefuente' => ['type' => 'decimal(18,4)', 'required' => true],
				'ecp_decReteIca' => ['type' => 'decimal(18,4)', 'required' => true],
				'ecp_decReteIva' => ['type' => 'decimal(18,4)', 'required' => true],
				'ecp_decValorNeto' => ['type' => 'decimal(18,4)', 'required' => true],
				'ecp_datFechaEmision' => ['type' => 'date', 'required' => true],
				'ecp_datFechaVencimiento' => ['type' => 'date', 'required' => true],
				'ecp_lonNota' => ['type' => 'str', 'required' => true],
				'ecp_dtimFechaCreacion' => ['type' => 'datetime'],
				'ecp_dtimFechaActualizacion' => ['type' => 'datetime'],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'doc_intIdDocumento' => ['type' => 'int', 'required' => true],
				'cen_intIdCentrocostos' => ['type' => 'int', 'required' => true],
				'est_intEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['cen_intIdCentrocostos', 'doc_intIdDocumento', 'est_intEstado', 'per_intIdPersona', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_pedido.cen_intIdCentrocostos']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_pedido.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pedido.est_intEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_pedido.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_pedido.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_pedido.usu_intIdActualizador']
				],
				'tbl_pedido_detalle' => [
					['tbl_pedido_detalle.ecp_intIdPedido','tbl_pedido.ecp_intId']
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
				        0 => 'tbl_pedido',
				        1 => 'cen_intIdCentrocostos',
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
				        0 => 'tbl_pedido',
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
				        0 => 'tbl_pedido',
				        1 => 'est_intEstado',
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
				        0 => 'tbl_pedido',
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
				        0 => 'tbl_pedido',
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
				        0 => 'tbl_pedido',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				  'tbl_pedido_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pedido_detalle',
				        1 => 'ecp_intIdPedido',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pedido',
				        1 => 'ecp_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_pedido.cen_intIdCentrocostos']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_pedido.doc_intIdDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pedido.est_intEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_pedido.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_pedido.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_pedido.usu_intIdActualizador']
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
				        0 => 'tbl_pedido',
				        1 => 'cen_intIdCentrocostos',
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
				        0 => 'tbl_pedido',
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
				        0 => 'tbl_pedido',
				        1 => 'est_intEstado',
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
				        0 => 'tbl_pedido',
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
				        0 => 'tbl_pedido',
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
				        0 => 'tbl_pedido',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}


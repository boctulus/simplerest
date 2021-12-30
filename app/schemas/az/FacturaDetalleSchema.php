<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FacturaDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'factura_detalle',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'factura_id' => 'INT',
				'product_id' => 'INT',
				'quantity' => 'INT',
				'created_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'factura_id' => ['type' => 'int', 'required' => true],
				'product_id' => ['type' => 'int', 'required' => true],
				'quantity' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
			],

			'fks' 			=> ['factura_id', 'product_id'],

			'relationships' => [
				'facturas' => [
					['facturas.id','factura_detalle.factura_id']
				],
				'products' => [
					['products.id','factura_detalle.product_id']
				]
			],

			'expanded_relationships' => array (
				  'facturas' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'facturas',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'factura_detalle',
				        1 => 'factura_id',
				      ),
				    ),
				  ),
				  'products' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'products',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'factura_detalle',
				        1 => 'product_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'facturas' => [
					['facturas.id','factura_detalle.factura_id']
				],
				'products' => [
					['products.id','factura_detalle.product_id']
				]
			],

			'expanded_relationships_from' => array (
				  'facturas' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'facturas',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'factura_detalle',
				        1 => 'factura_id',
				      ),
				    ),
				  ),
				  'products' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'products',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'factura_detalle',
				        1 => 'product_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}


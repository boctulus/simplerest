<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductValoracionesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_valoraciones',

			'id_name'		=> null,

			'attr_types'	=> [
				'product_id' => 'INT',
				'valoracion_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> [],

			'autoincrement' => null,

			'nullable'		=> ['updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'product_id' => ['type' => 'int', 'required' => true],
				'valoracion_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['product_id', 'valoracion_id'],

			'relationships' => [
				'products' => [
					['products.id','product_valoraciones.product_id']
				],
				'valoraciones' => [
					['valoraciones.id_val','product_valoraciones.valoracion_id']
				]
			],

			'expanded_relationships' => array (
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
				        0 => 'product_valoraciones',
				        1 => 'product_id',
				      ),
				    ),
				  ),
				  'valoraciones' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'valoraciones',
				        1 => 'id_val',
				      ),
				      1 => 
				      array (
				        0 => 'product_valoraciones',
				        1 => 'valoracion_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'products' => [
					['products.id','product_valoraciones.product_id']
				],
				'valoraciones' => [
					['valoraciones.id_val','product_valoraciones.valoracion_id']
				]
			],

			'expanded_relationships_from' => array (
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
				        0 => 'product_valoraciones',
				        1 => 'product_id',
				      ),
				    ),
				  ),
				  'valoraciones' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'valoraciones',
				        1 => 'id_val',
				      ),
				      1 => 
				      array (
				        0 => 'product_valoraciones',
				        1 => 'valoracion_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}


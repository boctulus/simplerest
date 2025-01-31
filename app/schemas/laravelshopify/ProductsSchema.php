<?php

namespace simplerest\schemas\laravelshopify;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'products',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'shopify_product_id', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'shopify_product_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'created_at', 'updated_at'],

			'required'			=> ['shopify_product_id'],

			'uniques'			=> ['shopify_product_id'],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'shopify_product_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> [],

			'relationships' => [
				'inventory' => [
					['inventory.product_id','products.id']
				],
				'price_rules' => [
					['price_rules.product_id','products.id']
				]
			],

			'expanded_relationships' => array (
  'inventory' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'inventory',
        1 => 'product_id',
      ),
      1 => 
      array (
        0 => 'products',
        1 => 'id',
      ),
    ),
  ),
  'price_rules' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'price_rules',
        1 => 'product_id',
      ),
      1 => 
      array (
        0 => 'products',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}


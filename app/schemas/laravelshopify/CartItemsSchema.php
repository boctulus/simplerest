<?php

namespace simplerest\schemas\laravelshopify;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CartItemsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'cart_items',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'cart_id', 'shopify_product_id', 'quantity', 'price', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'cart_id' => 'INT',
				'shopify_product_id' => 'INT',
				'quantity' => 'INT',
				'price' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'created_at', 'updated_at'],

			'required'			=> ['cart_id', 'shopify_product_id', 'quantity', 'price'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'cart_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'shopify_product_id' => ['type' => 'int', 'required' => true],
				'quantity' => ['type' => 'int', 'required' => true],
				'price' => ['type' => 'decimal(10,2)', 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['cart_id'],

			'relationships' => [
				'carts' => [
					['carts.id','cart_items.cart_id']
				]
			],

			'expanded_relationships' => array (
  'carts' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'carts',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'cart_items',
        1 => 'cart_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'carts' => [
					['carts.id','cart_items.cart_id']
				]
			],

			'expanded_relationships_from' => array (
  'carts' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'carts',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'cart_items',
        1 => 'cart_id',
      ),
    ),
  ),
)
		];
	}	
}


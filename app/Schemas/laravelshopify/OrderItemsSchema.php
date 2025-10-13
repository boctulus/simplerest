<?php

namespace Boctulus\Simplerest\Schemas\laravelshopify;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class OrderItemsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'order_items',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'order_id', 'shopify_product_id', 'quantity', 'price', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'order_id' => 'INT',
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

			'required'			=> ['order_id', 'shopify_product_id', 'quantity', 'price'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'order_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'shopify_product_id' => ['type' => 'int', 'required' => true],
				'quantity' => ['type' => 'int', 'required' => true],
				'price' => ['type' => 'decimal(10,2)', 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['order_id'],

			'relationships' => [
				'orders' => [
					['orders.id','order_items.order_id']
				]
			],

			'expanded_relationships' => array (
  'orders' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'orders',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'order_items',
        1 => 'order_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'orders' => [
					['orders.id','order_items.order_id']
				]
			],

			'expanded_relationships_from' => array (
  'orders' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'orders',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'order_items',
        1 => 'order_id',
      ),
    ),
  ),
)
		];
	}	
}


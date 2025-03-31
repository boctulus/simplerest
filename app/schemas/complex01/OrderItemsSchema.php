<?php

namespace Boctulus\Simplerest\Schemas\complex01;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class OrderItemsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'order_items',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'order_id', 'product_id', 'quantity'],

			'attr_types'		=> [
				'id' => 'INT',
				'order_id' => 'INT',
				'product_id' => 'INT',
				'quantity' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['order_id', 'product_id', 'quantity'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'order_id' => ['type' => 'int', 'required' => true],
				'product_id' => ['type' => 'int', 'required' => true],
				'quantity' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> ['order_id', 'product_id'],

			'relationships' => [
				'orders' => [
					['orders.id','order_items.order_id']
				],
				'products' => [
					['products.id','order_items.product_id']
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
        0 => 'order_items',
        1 => 'product_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'orders' => [
					['orders.id','order_items.order_id']
				],
				'products' => [
					['products.id','order_items.product_id']
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
        0 => 'order_items',
        1 => 'product_id',
      ),
    ),
  ),
)
		];
	}	
}


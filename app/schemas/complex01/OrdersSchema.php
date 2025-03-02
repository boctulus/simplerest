<?php

namespace simplerest\schemas\complex01;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class OrdersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'orders',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'customer_id', 'seller_id', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'customer_id' => 'INT',
				'seller_id' => 'INT',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'created_at'],

			'required'			=> ['customer_id', 'seller_id'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'customer_id' => ['type' => 'int', 'required' => true],
				'seller_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['customer_id', 'seller_id'],

			'relationships' => [
				'customers' => [
					['customers.id','orders.customer_id']
				],
				'sellers' => [
					['sellers.id','orders.seller_id']
				],
				'order_items' => [
					['order_items.order_id','orders.id']
				]
			],

			'expanded_relationships' => array (
  'customers' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'customers',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'orders',
        1 => 'customer_id',
      ),
    ),
  ),
  'sellers' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'sellers',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'orders',
        1 => 'seller_id',
      ),
    ),
  ),
  'order_items' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'order_items',
        1 => 'order_id',
      ),
      1 => 
      array (
        0 => 'orders',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'customers' => [
					['customers.id','orders.customer_id']
				],
				'sellers' => [
					['sellers.id','orders.seller_id']
				]
			],

			'expanded_relationships_from' => array (
  'customers' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'customers',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'orders',
        1 => 'customer_id',
      ),
    ),
  ),
  'sellers' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'sellers',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'orders',
        1 => 'seller_id',
      ),
    ),
  ),
)
		];
	}	
}


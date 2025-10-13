<?php

namespace Boctulus\Simplerest\Schemas\laravelshopify;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class OrdersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'orders',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'user_id', 'address_id', 'status', 'total', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'user_id' => 'INT',
				'address_id' => 'INT',
				'status' => 'STR',
				'total' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'created_at', 'updated_at'],

			'required'			=> ['user_id', 'address_id', 'status', 'total'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'user_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'address_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'status' => ['type' => 'str', 'max' => 255, 'required' => true],
				'total' => ['type' => 'decimal(10,2)', 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['address_id', 'user_id'],

			'relationships' => [
				'addresses' => [
					['addresses.id','orders.address_id']
				],
				'users' => [
					['users.id','orders.user_id']
				],
				'order_items' => [
					['order_items.order_id','orders.id']
				]
			],

			'expanded_relationships' => array (
  'addresses' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'addresses',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'orders',
        1 => 'address_id',
      ),
    ),
  ),
  'users' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'orders',
        1 => 'user_id',
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
				'addresses' => [
					['addresses.id','orders.address_id']
				],
				'users' => [
					['users.id','orders.user_id']
				]
			],

			'expanded_relationships_from' => array (
  'addresses' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'addresses',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'orders',
        1 => 'address_id',
      ),
    ),
  ),
  'users' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'orders',
        1 => 'user_id',
      ),
    ),
  ),
)
		];
	}	
}


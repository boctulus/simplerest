<?php

namespace simplerest\schemas\complex01;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class SellersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'sellers',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'user_id', 'referred_by'],

			'attr_types'		=> [
				'id' => 'INT',
				'user_id' => 'INT',
				'referred_by' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'referred_by'],

			'required'			=> ['user_id'],

			'uniques'			=> ['user_id'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'user_id' => ['type' => 'int', 'required' => true],
				'referred_by' => ['type' => 'int']
			],

			'fks' 				=> ['user_id', 'referred_by'],

			'relationships' => [
				'users' => [
					['users.id','sellers.user_id']
				],
				'sellers' => [
					['sellers.id','sellers.referred_by'],
					['sellers.referred_by','sellers.id']
				],
				'customers' => [
					['customers.assigned_seller','sellers.id']
				],
				'orders' => [
					['orders.seller_id','sellers.id']
				],
				'seller_products' => [
					['seller_products.seller_id','sellers.id']
				]
			],

			'expanded_relationships' => array (
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
        0 => 'sellers',
        1 => 'user_id',
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
        0 => 'sellers',
        1 => 'referred_by',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'sellers',
        1 => 'referred_by',
      ),
      1 => 
      array (
        0 => 'sellers',
        1 => 'id',
      ),
    ),
  ),
  'customers' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'customers',
        1 => 'assigned_seller',
      ),
      1 => 
      array (
        0 => 'sellers',
        1 => 'id',
      ),
    ),
  ),
  'orders' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'orders',
        1 => 'seller_id',
      ),
      1 => 
      array (
        0 => 'sellers',
        1 => 'id',
      ),
    ),
  ),
  'seller_products' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'seller_products',
        1 => 'seller_id',
      ),
      1 => 
      array (
        0 => 'sellers',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users.id','sellers.user_id']
				],
				'sellers' => [
					['sellers.id','sellers.referred_by']
				]
			],

			'expanded_relationships_from' => array (
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
        0 => 'sellers',
        1 => 'user_id',
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
        0 => 'sellers',
        1 => 'referred_by',
      ),
    ),
  ),
)
		];
	}	
}


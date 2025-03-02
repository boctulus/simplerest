<?php

namespace simplerest\schemas\complex01;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CustomersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'customers',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'user_id', 'assigned_seller'],

			'attr_types'		=> [
				'id' => 'INT',
				'user_id' => 'INT',
				'assigned_seller' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'assigned_seller'],

			'required'			=> ['user_id'],

			'uniques'			=> ['user_id'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'user_id' => ['type' => 'int', 'required' => true],
				'assigned_seller' => ['type' => 'int']
			],

			'fks' 				=> ['user_id', 'assigned_seller'],

			'relationships' => [
				'users' => [
					['users.id','customers.user_id']
				],
				'sellers' => [
					['sellers.id','customers.assigned_seller']
				],
				'customer_details' => [
					['customer_details.customer_id','customers.id']
				],
				'orders' => [
					['orders.customer_id','customers.id']
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
        0 => 'customers',
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
        0 => 'customers',
        1 => 'assigned_seller',
      ),
    ),
  ),
  'customer_details' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'customer_details',
        1 => 'customer_id',
      ),
      1 => 
      array (
        0 => 'customers',
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
        1 => 'customer_id',
      ),
      1 => 
      array (
        0 => 'customers',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users.id','customers.user_id']
				],
				'sellers' => [
					['sellers.id','customers.assigned_seller']
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
        0 => 'customers',
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
        0 => 'customers',
        1 => 'assigned_seller',
      ),
    ),
  ),
)
		];
	}	
}


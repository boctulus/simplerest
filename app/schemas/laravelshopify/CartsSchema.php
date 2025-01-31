<?php

namespace simplerest\schemas\laravelshopify;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CartsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'carts',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'user_id', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'user_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'created_at', 'updated_at'],

			'required'			=> ['user_id'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'user_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['user_id'],

			'relationships' => [
				'users' => [
					['users.id','carts.user_id']
				],
				'cart_items' => [
					['cart_items.cart_id','carts.id']
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
        0 => 'carts',
        1 => 'user_id',
      ),
    ),
  ),
  'cart_items' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cart_items',
        1 => 'cart_id',
      ),
      1 => 
      array (
        0 => 'carts',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users.id','carts.user_id']
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
        0 => 'carts',
        1 => 'user_id',
      ),
    ),
  ),
)
		];
	}	
}


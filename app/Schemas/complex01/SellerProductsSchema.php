<?php

namespace Boctulus\Simplerest\Schemas\complex01;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class SellerProductsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'seller_products',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'seller_id', 'product_id'],

			'attr_types'		=> [
				'id' => 'INT',
				'seller_id' => 'INT',
				'product_id' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['seller_id', 'product_id'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'seller_id' => ['type' => 'int', 'required' => true],
				'product_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> ['seller_id', 'product_id'],

			'relationships' => [
				'sellers' => [
					['sellers.id','seller_products.seller_id']
				],
				'products' => [
					['products.id','seller_products.product_id']
				]
			],

			'expanded_relationships' => array (
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
        0 => 'seller_products',
        1 => 'seller_id',
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
        0 => 'seller_products',
        1 => 'product_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'sellers' => [
					['sellers.id','seller_products.seller_id']
				],
				'products' => [
					['products.id','seller_products.product_id']
				]
			],

			'expanded_relationships_from' => array (
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
        0 => 'seller_products',
        1 => 'seller_id',
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
        0 => 'seller_products',
        1 => 'product_id',
      ),
    ),
  ),
)
		];
	}	
}


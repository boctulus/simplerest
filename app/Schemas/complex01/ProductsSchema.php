<?php

namespace Boctulus\Simplerest\Schemas\complex01;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ProductsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'products',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'category_id', 'name', 'price'],

			'attr_types'		=> [
				'id' => 'INT',
				'category_id' => 'INT',
				'name' => 'STR',
				'price' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['category_id', 'name', 'price'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'category_id' => ['type' => 'int', 'required' => true],
				'name' => ['type' => 'str', 'max' => 100, 'required' => true],
				'price' => ['type' => 'decimal(10,2)', 'required' => true]
			],

			'fks' 				=> ['category_id'],

			'relationships' => [
				'categories' => [
					['categories.id','products.category_id']
				],
				'order_items' => [
					['order_items.product_id','products.id']
				],
				'product_tags' => [
					['product_tags.product_id','products.id']
				],
				'seller_products' => [
					['seller_products.product_id','products.id']
				]
			],

			'expanded_relationships' => array (
  'categories' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'categories',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'products',
        1 => 'category_id',
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
        1 => 'product_id',
      ),
      1 => 
      array (
        0 => 'products',
        1 => 'id',
      ),
    ),
  ),
  'product_tags' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'product_tags',
        1 => 'product_id',
      ),
      1 => 
      array (
        0 => 'products',
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
				'categories' => [
					['categories.id','products.category_id']
				]
			],

			'expanded_relationships_from' => array (
  'categories' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'categories',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'products',
        1 => 'category_id',
      ),
    ),
  ),
)
		];
	}	
}


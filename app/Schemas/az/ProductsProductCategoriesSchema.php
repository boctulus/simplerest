<?php

namespace Boctulus\Simplerest\Schemas\az;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ProductsProductCategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'products_product_categories',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'product_id', 'product_category_id', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'product_id' => 'INT',
				'product_category_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'required'		=> ['product_id', 'product_category_id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'product_id' => ['type' => 'int', 'required' => true],
				'product_category_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'str'],
				'updated_at' => ['type' => 'str']
			],

			'fks' 			=> ['product_category_id', 'product_id'],

			'relationships' => [
				'product_categories' => [
					['product_categories.id','products_product_categories.product_category_id']
				],
				'products' => [
					['products.id','products_product_categories.product_id']
				]
			],

			'expanded_relationships' => array (
  'product_categories' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'product_categories',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'products_product_categories',
        1 => 'product_category_id',
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
        0 => 'products_product_categories',
        1 => 'product_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'product_categories' => [
					['product_categories.id','products_product_categories.product_category_id']
				],
				'products' => [
					['products.id','products_product_categories.product_id']
				]
			],

			'expanded_relationships_from' => array (
  'product_categories' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'product_categories',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'products_product_categories',
        1 => 'product_category_id',
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
        0 => 'products_product_categories',
        1 => 'product_id',
      ),
    ),
  ),
)
		];
	}	
}


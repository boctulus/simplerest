<?php

namespace simplerest\schemas\complex01;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'products',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'price'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR',
				'price' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['name', 'price'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 100, 'required' => true],
				'price' => ['type' => 'decimal(10,2)', 'required' => true]
			],

			'fks' 				=> [],

			'relationships' => [
				'order_items' => [
					['order_items.product_id','products.id']
				],
				'seller_products' => [
					['seller_products.product_id','products.id']
				]
			],

			'expanded_relationships' => array (
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
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}


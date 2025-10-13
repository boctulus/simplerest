<?php

namespace Boctulus\Simplerest\Schemas\laravelshopify;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class PriceRulesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'price_rules',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'product_id', 'wholesale_price', 'minimum_quantity', 'discount_percentage', 'active', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'product_id' => 'INT',
				'wholesale_price' => 'STR',
				'minimum_quantity' => 'INT',
				'discount_percentage' => 'STR',
				'active' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'discount_percentage', 'active', 'created_at', 'updated_at'],

			'required'			=> ['product_id', 'wholesale_price', 'minimum_quantity'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'product_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'wholesale_price' => ['type' => 'decimal(10,2)', 'required' => true],
				'minimum_quantity' => ['type' => 'int', 'required' => true],
				'discount_percentage' => ['type' => 'decimal(5,2)'],
				'active' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['product_id'],

			'relationships' => [
				'products' => [
					['products.id','price_rules.product_id']
				]
			],

			'expanded_relationships' => array (
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
        0 => 'price_rules',
        1 => 'product_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'products' => [
					['products.id','price_rules.product_id']
				]
			],

			'expanded_relationships_from' => array (
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
        0 => 'price_rules',
        1 => 'product_id',
      ),
    ),
  ),
)
		];
	}	
}


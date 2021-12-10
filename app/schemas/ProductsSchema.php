<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'products',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'parent_id' => 'INT',
				'name' => 'STR',
				'type' => 'STR',
				'price' => 'STR',
				'prev_price' => 'STR',
				'description' => 'STR',
				'short_description' => 'STR',
				'slug' => 'STR',
				'images' => 'STR',
				'categories' => 'STR',
				'tags' => 'STR',
				'dimensions' => 'STR',
				'attributes' => 'STR',
				'sku' => 'STR',
				'status' => 'STR',
				'remote_id' => 'STR',
				'stock_status' => 'STR',
				'url_ori' => 'STR',
				'posted' => 'INT',
				'comment' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'parent_id', 'type', 'price', 'prev_price', 'short_description', 'categories', 'tags', 'dimensions', 'attributes', 'sku', 'status', 'remote_id', 'stock_status', 'url_ori', 'posted', 'comment', 'updated_at'],

			'uniques'		=> ['slug', 'url_ori'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'parent_id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 80, 'required' => true],
				'type' => ['type' => 'str', 'max' => 20],
				'price' => ['type' => 'str'],
				'prev_price' => ['type' => 'str'],
				'description' => ['type' => 'str', 'required' => true],
				'short_description' => ['type' => 'str', 'max' => 512],
				'slug' => ['type' => 'str', 'max' => 100, 'required' => true],
				'images' => ['type' => 'str', 'required' => true],
				'categories' => ['type' => 'str', 'max' => 250],
				'tags' => ['type' => 'str', 'max' => 250],
				'dimensions' => ['type' => 'str'],
				'attributes' => ['type' => 'str'],
				'sku' => ['type' => 'str', 'max' => 50],
				'status' => ['type' => 'str', 'max' => 20],
				'remote_id' => ['type' => 'str', 'max' => 60],
				'stock_status' => ['type' => 'str', 'max' => 30],
				'url_ori' => ['type' => 'str', 'max' => 300],
				'posted' => ['type' => 'bool'],
				'comment' => ['type' => 'str', 'max' => 200],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['parent_id'],

			'relationships' => [
				'products' => [
					['products.id','products.parent_id'],
					['products.parent_id','products.id']
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
				        0 => 'products',
				        1 => 'parent_id',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'products',
				        1 => 'parent_id',
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
				'products' => [
					['products.id','products.parent_id']
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
				        0 => 'products',
				        1 => 'parent_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}


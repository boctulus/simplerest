<?php

namespace simplerest\schemas\ef;

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
				'name' => 'STR',
				'type' => 'STR',
				'regular_price' => 'STR',
				'sale_price' => 'STR',
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
				'stock' => 'INT',
				'stock_status' => 'STR',
				'url_ori' => 'STR',
				'posted' => 'INT',
				'comment' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'type', 'regular_price', 'sale_price', 'short_description', 'slug', 'images', 'categories', 'tags', 'dimensions', 'attributes', 'sku', 'status', 'stock', 'stock_status', 'url_ori', 'posted', 'comment', 'updated_at'],

			'uniques'		=> ['slug', 'sku', 'url_ori'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 80, 'required' => true],
				'type' => ['type' => 'str', 'max' => 20],
				'regular_price' => ['type' => 'str'],
				'sale_price' => ['type' => 'str'],
				'description' => ['type' => 'str', 'required' => true],
				'short_description' => ['type' => 'str', 'max' => 512],
				'slug' => ['type' => 'str', 'max' => 255],
				'images' => ['type' => 'str'],
				'categories' => ['type' => 'str', 'max' => 250],
				'tags' => ['type' => 'str', 'max' => 250],
				'dimensions' => ['type' => 'str'],
				'attributes' => ['type' => 'str'],
				'sku' => ['type' => 'str', 'max' => 50],
				'status' => ['type' => 'str', 'max' => 20],
				'stock' => ['type' => 'int'],
				'stock_status' => ['type' => 'str', 'max' => 30],
				'url_ori' => ['type' => 'str', 'max' => 300],
				'posted' => ['type' => 'bool'],
				'comment' => ['type' => 'str', 'max' => 200],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}


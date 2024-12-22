<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'products',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'type', 'regular_price', 'sale_price', 'description', 'short_description', 'slug', 'images', 'categories', 'tags', 'dimensions', 'attributes', 'sku', 'status', 'stock', 'stock_status', 'url_ori', 'posted', 'comment', 'created_at', 'updated_at', 'cost', 'size', 'belongs_to', 'active', 'locked', 'workspace', 'deleted_at'],

			'attr_types'		=> [
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
				'updated_at' => 'STR',
				'cost' => 'STR',
				'size' => 'STR',
				'belongs_to' => 'INT',
				'active' => 'INT',
				'locked' => 'INT',
				'workspace' => 'STR',
				'deleted_at' => 'STR'
			],

			'attr_type_detail'	=> [
				'images' => 'JSON',
				'dimensions' => 'JSON',
				'attributes' => 'JSON'
			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'type', 'regular_price', 'sale_price', 'short_description', 'categories', 'tags', 'dimensions', 'attributes', 'sku', 'status', 'stock', 'stock_status', 'url_ori', 'posted', 'comment', 'updated_at', 'cost', 'size', 'belongs_to', 'active', 'locked', 'workspace', 'deleted_at'],

			'required'			=> ['name', 'description', 'slug', 'images', 'created_at'],

			'uniques'			=> ['slug', 'url_ori'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 80, 'required' => true],
				'type' => ['type' => 'str', 'max' => 20],
				'regular_price' => ['type' => 'str'],
				'sale_price' => ['type' => 'str'],
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
				'stock' => ['type' => 'int'],
				'stock_status' => ['type' => 'str', 'max' => 30],
				'url_ori' => ['type' => 'str', 'max' => 300],
				'posted' => ['type' => 'bool'],
				'comment' => ['type' => 'str', 'max' => 200],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime'],
				'cost' => ['type' => 'decimal(10,2)'],
				'size' => ['type' => 'str', 'max' => 20],
				'belongs_to' => ['type' => 'int'],
				'active' => ['type' => 'bool'],
				'locked' => ['type' => 'bool'],
				'workspace' => ['type' => 'str', 'max' => 50],
				'deleted_at' => ['type' => 'timestamp']
			],

			'fks' 				=> [],

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


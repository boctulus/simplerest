<?php

namespace simplerest\models\schemas;

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
				'description' => 'STR',
				'size' => 'STR',
				'cost' => 'INT',
				'created_at' => 'STR',
				'created_by' => 'INT',
				'updated_at' => 'STR',
				'updated_by' => 'INT',
				'deleted_at' => 'STR',
				'deleted_by' => 'INT',
				'active' => 'INT',
				'locked' => 'INT',
				'workspace' => 'STR',
				'belongs_to' => 'INT',
				'category' => 'INT'
			],

			'nullable'		=> ['id', 'description', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by', 'active', 'locked', 'workspace', 'belongs_to', 'category'],

			'rules' 		=> [
				'name' => ['max' => 50],
				'description' => ['max' => 240],
				'size' => ['max' => 30],
				'workspace' => ['max' => 40]
			],

			'relationships' => [
				'users' => [
					['users.id','products.belongs_to']
				],
				'product_categories' => [
					['product_categories.id_catego','products.category']
				],
				'product_comments' => [
					['product_comments.product_id','products.id']
				],
				'products_product_categories' => [
					['products_product_categories.product_id','products.id']
				]
			]
		];
	}	
}


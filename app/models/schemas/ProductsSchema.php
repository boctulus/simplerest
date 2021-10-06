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
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 50, 'required' => true],
				'description' => ['type' => 'str', 'max' => 240],
				'size' => ['type' => 'str', 'max' => 30, 'required' => true],
				'cost' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime'],
				'created_by' => ['type' => 'int'],
				'updated_at' => ['type' => 'datetime'],
				'updated_by' => ['type' => 'int'],
				'deleted_at' => ['type' => 'datetime'],
				'deleted_by' => ['type' => 'int'],
				'active' => ['type' => 'bool'],
				'locked' => ['type' => 'bool'],
				'workspace' => ['type' => 'str', 'max' => 40],
				'belongs_to' => ['type' => 'int'],
				'category' => ['type' => 'int']
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


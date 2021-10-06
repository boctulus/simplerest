<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductsProductCategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'products_product_categories',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'product_id' => 'INT',
				'product_category_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'product_id' => ['type' => 'int', 'required' => true],
				'product_category_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'str'],
				'updated_at' => ['type' => 'str']
			],

			'relationships' => [
				'products' => [
					['products.id','products_product_categories.product_id']
				],
				'product_categories' => [
					['product_categories.id_catego','products_product_categories.product_category_id']
				]
			]
		];
	}	
}


<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductCategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_categories',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 80, 'required' => true]
			],

			'fks' 			=> [],

			'relationships' => [
				'products_product_categories' => [
					['products_product_categories.product_category_id','product_categories.id']
				]
			],

			'expanded_relationships' => array (
  'products_product_categories' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'products_product_categories',
        1 => 'product_category_id',
      ),
      1 => 
      array (
        0 => 'product_categories',
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


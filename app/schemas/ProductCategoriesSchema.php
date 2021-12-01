<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductCategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_categories',

			'id_name'		=> 'id_catego',

			'attr_types'	=> [
				'id_catego' => 'INT',
				'name_catego' => 'STR'
			],

			'primary'		=> ['id_catego'],

			'autoincrement' => 'id_catego',

			'nullable'		=> ['id_catego'],

			'uniques'		=> [],

			'rules' 		=> [
				'id_catego' => ['type' => 'int'],
				'name_catego' => ['type' => 'str', 'max' => 80, 'required' => true]
			],

			'fks' 			=> [],

			'relationships' => [
				'products' => [
					['products.category','product_categories.id_catego']
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
				        1 => 'category',
				      ),
				      1 => 
				      array (
				        0 => 'product_categories',
				        1 => 'id_catego',
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


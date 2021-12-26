<?php

namespace simplerest\schemas\az;

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
				'name_catego' => 'STR',
				'otro_campo' => 'STR'
			],

			'primary'		=> ['id_catego'],

			'autoincrement' => 'id_catego',

			'nullable'		=> ['id_catego', 'otro_campo'],

			'uniques'		=> [],

			'rules' 		=> [
				'id_catego' => ['type' => 'int'],
				'name_catego' => ['type' => 'str', 'max' => 80, 'required' => true],
				'otro_campo' => ['type' => 'str', 'max' => 30]
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


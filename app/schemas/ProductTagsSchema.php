<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductTagsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_tags',

			'id_name'		=> 'id_tag',

			'attr_types'	=> [
				'id_tag' => 'INT',
				'name' => 'STR',
				'comment' => 'STR',
				'product_id' => 'INT'
			],

			'primary'		=> ['id_tag'],

			'autoincrement' => 'id_tag',

			'nullable'		=> ['id_tag', 'comment'],

			'uniques'		=> [],

			'rules' 		=> [
				'id_tag' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 30, 'required' => true],
				'comment' => ['type' => 'str', 'max' => 60],
				'product_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['product_id'],

			'relationships' => [
				'products' => [
					['products.id','product_tags.product_id']
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
				        0 => 'product_tags',
				        1 => 'product_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'products' => [
					['products.id','product_tags.product_id']
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
				        0 => 'product_tags',
				        1 => 'product_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}


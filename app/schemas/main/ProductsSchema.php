<?php

namespace simplerest\schemas\main;

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
				'is_locked' => 'INT',
				'workspace' => 'STR',
				'belongs_to' => 'INT',
				'category' => 'INT',
				'digital_id' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'description', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by', 'active', 'is_locked', 'workspace', 'belongs_to', 'category'],

			'uniques'		=> [],

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
				'is_locked' => ['type' => 'bool'],
				'workspace' => ['type' => 'str', 'max' => 40],
				'belongs_to' => ['type' => 'int'],
				'category' => ['type' => 'int'],
				'digital_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['belongs_to', 'deleted_by', 'digital_id', 'category'],

			'relationships' => [
				'users' => [
					['users|__belongs_to.id','products.belongs_to'],
					['users|__deleted_by.id','products.deleted_by']
				],
				'digital_products' => [
					['digital_products.id','products.digital_id']
				],
				'product_categories' => [
					['product_categories.id_catego','products.category']
				],
				'product_tags' => [
					['product_tags.product_id','products.id']
				],
				'product_valoraciones' => [
					['product_valoraciones.product_id','products.id']
				],
				'product_comments' => [
					['product_comments.product_id','products.id']
				]
			],

			'expanded_relationships' => array (
				  'digital_products' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'digital_products',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'digital_id',
				      ),
				    ),
				  ),
				  'users' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'users',
				        1 => 'id',
				        'alias' => '__belongs_to',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'belongs_to',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'users',
				        1 => 'id',
				        'alias' => '__deleted_by',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'deleted_by',
				      ),
				    ),
				  ),
				  'product_categories' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'product_categories',
				        1 => 'id_catego',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'category',
				      ),
				    ),
				  ),
				  'product_valoraciones' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'product_valoraciones',
				        1 => 'product_id',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'id',
				      ),
				    ),
				  ),
				  'product_comments' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'product_comments',
				        1 => 'product_id',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'id',
				      ),
				    ),
				  ),
				  'product_tags' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'product_tags',
				        1 => 'product_id',
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
				'digital_products' => [
					['digital_products.id','products.digital_id']
				],
				'users' => [
					['users|__belongs_to.id','products.belongs_to'],
					['users|__deleted_by.id','products.deleted_by']
				],
				'product_categories' => [
					['product_categories.id_catego','products.category']
				]
			],

			'expanded_relationships_from' => array (
				  'users' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'users',
				        1 => 'id',
				        'alias' => '__deleted_by',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'deleted_by',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'users',
				        1 => 'id',
				        'alias' => '__belongs_to',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'belongs_to',
				      ),
				    ),
				  ),
				  'digital_products' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'digital_products',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'digital_id',
				      ),
				    ),
				  ),
				  'product_categories' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'product_categories',
				        1 => 'id_catego',
				      ),
				      1 => 
				      array (
				        0 => 'products',
				        1 => 'category',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\schemas\az;

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
				'belongs_to' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'description', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by', 'active', 'is_locked', 'workspace', 'belongs_to'],

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
				'belongs_to' => ['type' => 'int']
			],

			'fks' 			=> ['belongs_to'],

			'relationships' => [
				'users' => [
					['users.id','products.belongs_to']
				],
				'product_comments' => [
					['product_comments.product_id','products.id']
				],
				'products_product_categories' => [
					['products_product_categories.product_id','products.id']
				]
			],

			'expanded_relationships' => array (
  'users' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'products',
        1 => 'belongs_to',
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
  'products_product_categories' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'products_product_categories',
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
				'users' => [
					['users.id','products.belongs_to']
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
      ),
      1 => 
      array (
        0 => 'products',
        1 => 'belongs_to',
      ),
    ),
  ),
)
		];
	}	
}


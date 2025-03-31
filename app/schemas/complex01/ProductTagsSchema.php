<?php

namespace Boctulus\Simplerest\Schemas\complex01;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ProductTagsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'product_tags',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'product_id', 'tag_id'],

			'attr_types'		=> [
				'id' => 'INT',
				'product_id' => 'INT',
				'tag_id' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['product_id', 'tag_id'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'product_id' => ['type' => 'int', 'required' => true],
				'tag_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> ['product_id', 'tag_id'],

			'relationships' => [
				'products' => [
					['products.id','product_tags.product_id']
				],
				'tags' => [
					['tags.id','product_tags.tag_id']
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
  'tags' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tags',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'product_tags',
        1 => 'tag_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'products' => [
					['products.id','product_tags.product_id']
				],
				'tags' => [
					['tags.id','product_tags.tag_id']
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
  'tags' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tags',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'product_tags',
        1 => 'tag_id',
      ),
    ),
  ),
)
		];
	}	
}


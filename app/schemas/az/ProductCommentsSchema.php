<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductCommentsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_comments',

			'id_name'		=> 'product_id',

			'attr_types'	=> [
				'id' => 'INT',
				'text' => 'STR',
				'product_id' => 'INT'
			],

			'primary'		=> ['product_id'],

			'autoincrement' => null,

			'nullable'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'required' => true],
				'text' => ['type' => 'str', 'max' => 144, 'required' => true],
				'product_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['product_id'],

			'relationships' => [
				'products' => [
					['products.id','product_comments.product_id']
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
        0 => 'product_comments',
        1 => 'product_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'products' => [
					['products.id','product_comments.product_id']
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
        0 => 'product_comments',
        1 => 'product_id',
      ),
    ),
  ),
)
		];
	}	
}


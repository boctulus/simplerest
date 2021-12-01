<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductCommentsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_comments',

			'id_name'		=> null,

			'attr_types'	=> [
				'product_id' => 'INT',
				'comment_id' => 'INT',
				'created_at' => 'STR',
				'updated_by' => 'STR'
			],

			'primary'		=> [],

			'autoincrement' => null,

			'nullable'		=> ['created_at', 'updated_by'],

			'uniques'		=> [],

			'rules' 		=> [
				'product_id' => ['type' => 'int', 'required' => true],
				'comment_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime'],
				'updated_by' => ['type' => 'datetime']
			],

			'fks' 			=> ['comment_id', 'product_id'],

			'relationships' => [
				'comments' => [
					['comments.id','product_comments.comment_id']
				],
				'products' => [
					['products.id','product_comments.product_id']
				]
			],

			'expanded_relationships' => array (
				  'comments' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'comments',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'product_comments',
				        1 => 'comment_id',
				      ),
				    ),
				  ),
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
				'comments' => [
					['comments.id','product_comments.comment_id']
				],
				'products' => [
					['products.id','product_comments.product_id']
				]
			],

			'expanded_relationships_from' => array (
				  'comments' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'comments',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'product_comments',
				        1 => 'comment_id',
				      ),
				    ),
				  ),
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


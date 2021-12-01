<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CommentsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'comments',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'text' => 'STR',
				'product_id' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> ['product_id'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'text' => ['type' => 'str', 'max' => 144, 'required' => true],
				'product_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> [],

			'relationships' => [
				'product_comments' => [
					['product_comments.comment_id','comments.id']
				]
			],

			'expanded_relationships' => array (
				  'product_comments' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'product_comments',
				        1 => 'comment_id',
				      ),
				      1 => 
				      array (
				        0 => 'comments',
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


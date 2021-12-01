<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class BookReviewsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'book_reviews',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'text' => 'STR',
				'book_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'text' => ['type' => 'str', 'max' => 144, 'required' => true],
				'book_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['book_id'],

			'relationships' => [
				'books' => [
					['books.id','book_reviews.book_id']
				]
			],

			'expanded_relationships' => array (
				  'books' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'books',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'book_reviews',
				        1 => 'book_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'books' => [
					['books.id','book_reviews.book_id']
				]
			],

			'expanded_relationships_from' => array (
				  'books' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'books',
				        1 => 'id',
				      ),
				      1 => 
				      array (
				        0 => 'book_reviews',
				        1 => 'book_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace Boctulus\Simplerest\Schemas\az;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class BooksSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'books',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'name', 'author_id', 'editor_id'],

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR',
				'author_id' => 'INT',
				'editor_id' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'required'		=> ['name', 'author_id', 'editor_id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 60, 'required' => true],
				'author_id' => ['type' => 'int', 'required' => true],
				'editor_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['author_id', 'editor_id'],

			'relationships' => [
				'users' => [
					['users|__author_id.id','books.author_id'],
					['users|__editor_id.id','books.editor_id']
				],
				'book_reviews' => [
					['book_reviews.book_id','books.id']
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
        'alias' => '__author_id',
      ),
      1 => 
      array (
        0 => 'books',
        1 => 'author_id',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
        'alias' => '__editor_id',
      ),
      1 => 
      array (
        0 => 'books',
        1 => 'editor_id',
      ),
    ),
  ),
  'book_reviews' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'book_reviews',
        1 => 'book_id',
      ),
      1 => 
      array (
        0 => 'books',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users|__author_id.id','books.author_id'],
					['users|__editor_id.id','books.editor_id']
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
        'alias' => '__author_id',
      ),
      1 => 
      array (
        0 => 'books',
        1 => 'author_id',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
        'alias' => '__editor_id',
      ),
      1 => 
      array (
        0 => 'books',
        1 => 'editor_id',
      ),
    ),
  ),
)
		];
	}	
}


<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class BooksSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'books',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'name' => 'STR',
				'author_id' => 'INT',
				'editor_id' => 'INT'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 60, 'required' => true],
				'author_id' => ['type' => 'int', 'required' => true],
				'editor_id' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'users' => [
					['users.id','books.editor_id'],
					['users.id','books.author_id']
				],
				'book_reviews' => [
					['book_reviews.book_id','books.id']
				]
			]
		];
	}	
}


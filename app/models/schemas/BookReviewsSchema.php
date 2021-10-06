<?php

namespace simplerest\models\schemas;

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

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'text' => ['type' => 'str', 'max' => 144, 'required' => true],
				'book_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
			],

			'relationships' => [
				'books' => [
					['books.id','book_reviews.book_id']
				]
			]
		];
	}	
}


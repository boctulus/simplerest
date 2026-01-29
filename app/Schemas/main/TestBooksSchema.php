<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class TestBooksSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'test_books',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'title', 'isbn', 'author_id', 'pages', 'price', 'published_year', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'title' => 'STR',
				'isbn' => 'STR',
				'author_id' => 'INT',
				'pages' => 'INT',
				'price' => 'STR',
				'published_year' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'isbn', 'pages', 'price', 'published_year', 'updated_at', 'deleted_at'],

			'required'			=> ['title', 'author_id', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'title' => ['type' => 'str', 'max' => 200, 'required' => true],
				'isbn' => ['type' => 'str', 'max' => 20],
				'author_id' => ['type' => 'int', 'required' => true],
				'pages' => ['type' => 'int'],
				'price' => ['type' => 'decimal(10,2)'],
				'published_year' => ['type' => 'str'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime'],
				'deleted_at' => ['type' => 'datetime']
			],

			'fks' 				=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}


<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class TestOrmBooksSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'test_orm_books',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'title', 'author_id', 'price', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'title' => 'STR',
				'author_id' => 'INT',
				'price' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'price', 'updated_at', 'deleted_at'],

			'required'			=> ['title', 'author_id', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'title' => ['type' => 'str', 'max' => 200, 'required' => true],
				'author_id' => ['type' => 'int', 'required' => true],
				'price' => ['type' => 'decimal(10,2)'],
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


<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class TestOrmAuthorsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'test_orm_authors',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'email', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR',
				'email' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'email', 'updated_at', 'deleted_at'],

			'required'			=> ['name', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 100, 'required' => true],
				'email' => ['type' => 'str', 'max' => 100],
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


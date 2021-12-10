<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductCategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_categories',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'remote_id' => 'STR',
				'name' => 'STR',
				'slug' => 'STR',
				'description' => 'STR',
				'keywords' => 'STR',
				'parent_id' => 'INT',
				'parent_url' => 'STR',
				'image' => 'STR',
				'url' => 'STR',
				'pages' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'remote_id', 'description', 'keywords', 'parent_id', 'parent_url', 'image', 'url', 'pages'],

			'uniques'		=> ['slug'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'remote_id' => ['type' => 'str', 'max' => 50],
				'name' => ['type' => 'str', 'max' => 90, 'required' => true],
				'slug' => ['type' => 'str', 'max' => 250, 'required' => true],
				'description' => ['type' => 'str'],
				'keywords' => ['type' => 'str'],
				'parent_id' => ['type' => 'int'],
				'parent_url' => ['type' => 'str', 'max' => 60],
				'image' => ['type' => 'str', 'max' => 255],
				'url' => ['type' => 'str', 'max' => 255],
				'pages' => ['type' => 'int']
			],

			'fks' 			=> [],

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


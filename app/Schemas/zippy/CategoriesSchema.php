<?php

namespace Boctulus\Simplerest\Schemas\zippy;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'categories',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'slug', 'image_url', 'store_id', 'parent_id', 'parent_slug', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'		=> [
				'id' => 'STR',
				'name' => 'STR',
				'slug' => 'STR',
				'image_url' => 'STR',
				'store_id' => 'STR',
				'parent_id' => 'STR',
				'parent_slug' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> null,

			'nullable'			=> ['image_url', 'store_id', 'parent_id', 'parent_slug', 'updated_at', 'deleted_at'],

			'required'			=> ['id', 'name', 'slug', 'created_at'],

			'uniques'			=> ['slug'],

			'rules' 			=> [
				'id' => ['type' => 'str', 'max' => 21, 'required' => true],
				'name' => ['type' => 'str', 'max' => 150, 'required' => true],
				'slug' => ['type' => 'str', 'max' => 150, 'required' => true],
				'image_url' => ['type' => 'str', 'max' => 255],
				'store_id' => ['type' => 'str', 'max' => 30],
				'parent_id' => ['type' => 'str', 'max' => 21],
				'parent_slug' => ['type' => 'str', 'max' => 150],
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


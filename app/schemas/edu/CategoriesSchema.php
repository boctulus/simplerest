<?php

namespace simplerest\schemas\edu;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'categories',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'updated_at'],

			'required'			=> ['name', 'created_at'],

			'uniques'			=> ['name'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 100, 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 				=> [],

			'relationships' => [
				'courses' => [
					['courses.category_id','categories.id']
				]
			],

			'expanded_relationships' => array (
  'courses' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'courses',
        1 => 'category_id',
      ),
      1 => 
      array (
        0 => 'categories',
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


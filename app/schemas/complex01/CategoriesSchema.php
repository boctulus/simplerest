<?php

namespace simplerest\schemas\complex01;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'categories',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['name'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 100, 'required' => true]
			],

			'fks' 				=> [],

			'relationships' => [
				'products' => [
					['products.category_id','categories.id']
				]
			],

			'expanded_relationships' => array (
  'products' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'products',
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


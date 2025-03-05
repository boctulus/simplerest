<?php

namespace simplerest\schemas\complex01;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TagsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'tags',

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
				'product_tags' => [
					['product_tags.tag_id','tags.id']
				]
			],

			'expanded_relationships' => array (
  'product_tags' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'product_tags',
        1 => 'tag_id',
      ),
      1 => 
      array (
        0 => 'tags',
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


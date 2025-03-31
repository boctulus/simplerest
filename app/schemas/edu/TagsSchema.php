<?php

namespace Boctulus\Simplerest\Schemas\edu;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class TagsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'tags',

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
				'course_tag' => [
					['course_tag.tag_id','tags.id']
				]
			],

			'expanded_relationships' => array (
  'course_tag' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'course_tag',
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


<?php

namespace simplerest\schemas\edu;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CourseTagSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'course_tag',

			'id_name'			=> null,

			'fields'			=> ['course_id', 'tag_id'],

			'attr_types'		=> [
				'course_id' => 'INT',
				'tag_id' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> [],

			'autoincrement' 	=> null,

			'nullable'			=> [],

			'required'			=> ['course_id', 'tag_id'],

			'uniques'			=> [],

			'rules' 			=> [
				'course_id' => ['type' => 'int', 'required' => true],
				'tag_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> ['course_id', 'tag_id'],

			'relationships' => [
				'courses' => [
					['courses.id','course_tag.course_id']
				],
				'tags' => [
					['tags.id','course_tag.tag_id']
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
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'course_tag',
        1 => 'course_id',
      ),
    ),
  ),
  'tags' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tags',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'course_tag',
        1 => 'tag_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'courses' => [
					['courses.id','course_tag.course_id']
				],
				'tags' => [
					['tags.id','course_tag.tag_id']
				]
			],

			'expanded_relationships_from' => array (
  'courses' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'courses',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'course_tag',
        1 => 'course_id',
      ),
    ),
  ),
  'tags' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tags',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'course_tag',
        1 => 'tag_id',
      ),
    ),
  ),
)
		];
	}	
}


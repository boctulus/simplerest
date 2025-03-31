<?php

namespace Boctulus\Simplerest\Schemas\edu;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CourseDetailsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'course_details',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'course_id', 'description', 'duration', 'difficulty', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'course_id' => 'INT',
				'description' => 'STR',
				'duration' => 'INT',
				'difficulty' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'updated_at'],

			'required'			=> ['course_id', 'description', 'duration', 'difficulty', 'created_at'],

			'uniques'			=> ['course_id'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'course_id' => ['type' => 'int', 'required' => true],
				'description' => ['type' => 'str', 'required' => true],
				'duration' => ['type' => 'int', 'required' => true],
				'difficulty' => ['type' => 'str', 'max' => 50, 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 				=> ['course_id'],

			'relationships' => [
				'courses' => [
					['courses.id','course_details.course_id']
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
        0 => 'course_details',
        1 => 'course_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'courses' => [
					['courses.id','course_details.course_id']
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
        0 => 'course_details',
        1 => 'course_id',
      ),
    ),
  ),
)
		];
	}	
}


<?php

namespace Boctulus\Simplerest\Schemas\edu;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CourseStudentSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'course_student',

			'id_name'			=> null,

			'fields'			=> ['course_id', 'user_id'],

			'attr_types'		=> [
				'course_id' => 'INT',
				'user_id' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> [],

			'autoincrement' 	=> null,

			'nullable'			=> [],

			'required'			=> ['course_id', 'user_id'],

			'uniques'			=> [],

			'rules' 			=> [
				'course_id' => ['type' => 'int', 'required' => true],
				'user_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> ['course_id', 'user_id'],

			'relationships' => [
				'courses' => [
					['courses.id','course_student.course_id']
				],
				'users' => [
					['users.id','course_student.user_id']
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
        0 => 'course_student',
        1 => 'course_id',
      ),
    ),
  ),
  'users' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'course_student',
        1 => 'user_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'courses' => [
					['courses.id','course_student.course_id']
				],
				'users' => [
					['users.id','course_student.user_id']
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
        0 => 'course_student',
        1 => 'course_id',
      ),
    ),
  ),
  'users' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'course_student',
        1 => 'user_id',
      ),
    ),
  ),
)
		];
	}	
}


<?php

namespace Boctulus\Simplerest\Schemas\edu;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CoursesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'courses',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'title', 'active', 'category_id', 'professor_id', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'title' => 'STR',
				'active' => 'INT',
				'category_id' => 'INT',
				'professor_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'active', 'updated_at'],

			'required'			=> ['title', 'category_id', 'professor_id', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'title' => ['type' => 'str', 'max' => 150, 'required' => true],
				'active' => ['type' => 'bool'],
				'category_id' => ['type' => 'int', 'required' => true],
				'professor_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 				=> ['category_id', 'professor_id'],

			'relationships' => [
				'categories' => [
					['categories.id','courses.category_id']
				],
				'users' => [
					['users.id','courses.professor_id']
				],
				'course_details' => [
					['course_details.course_id','courses.id']
				],
				'course_student' => [
					['course_student.course_id','courses.id']
				],
				'course_tag' => [
					['course_tag.course_id','courses.id']
				]
			],

			'expanded_relationships' => array (
        'categories' => 
        array (
          0 => 
          array (
            0 => 
            array (
              0 => 'categories',
              1 => 'id',
            ),
            1 => 
            array (
              0 => 'courses',
              1 => 'category_id',
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
              0 => 'courses',
              1 => 'professor_id',
            ),
          ),
        ),
        'course_details' => 
        array (
          0 => 
          array (
            0 => 
            array (
              0 => 'course_details',
              1 => 'course_id',
            ),
            1 => 
            array (
              0 => 'courses',
              1 => 'id',
            ),
          ),
        ),
        'course_student' => 
        array (
          0 => 
          array (
            0 => 
            array (
              0 => 'course_student',
              1 => 'course_id',
            ),
            1 => 
            array (
              0 => 'courses',
              1 => 'id',
            ),
          ),
        ),
        'course_tag' => 
        array (
          0 => 
          array (
            0 => 
            array (
              0 => 'course_tag',
              1 => 'course_id',
            ),
            1 => 
            array (
              0 => 'courses',
              1 => 'id',
            ),
          ),
        ),
      ),

			'relationships_from' => [
				'categories' => [
					['categories.id','courses.category_id']
				],
				'users' => [
					['users.id','courses.professor_id']
				]
			],

			'expanded_relationships_from' => array (
        'categories' => 
        array (
          0 => 
          array (
            0 => 
            array (
              0 => 'categories',
              1 => 'id',
            ),
            1 => 
            array (
              0 => 'courses',
              1 => 'category_id',
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
              0 => 'courses',
              1 => 'professor_id',
            ),
          ),
        ),
      )
		];
	}	
}


<?php

namespace simplerest\schemas\edu;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'users',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'updated_at'],

			'required'			=> ['created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 				=> [],

			'relationships' => [
				'course_student' => [
					['course_student.user_id','users.id']
				],
				'courses' => [
					['courses.professor_id','users.id']
				]
			],

			'expanded_relationships' => array (
  'course_student' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'course_student',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'courses' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'courses',
        1 => 'professor_id',
      ),
      1 => 
      array (
        0 => 'users',
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


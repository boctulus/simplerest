<?php

namespace Boctulus\Simplerest\Schemas\edu;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'users',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR',
				'email' => 'STR',
				'role' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'updated_at'],

			'required'			=> ['name', 'email', 'role', 'created_at'],

			'uniques'			=> ['email'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 100, 'required' => true],
				'email' => ['type' => 'str', 'max' => 150, 'required' => true],
				'role' => ['type' => 'str', 'required' => true],
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


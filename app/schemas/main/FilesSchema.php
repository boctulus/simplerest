<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class FilesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'files',

			'id_name'			=> 'uuid',

			'fields'			=> ['uuid', 'filename', 'file_ext', 'filename_as_stored', 'belongs_to', 'guest_access', 'is_locked', 'broken', 'created_at', 'deleted_at'],

			'attr_types'		=> [
				'uuid' => 'STR',
				'filename' => 'STR',
				'file_ext' => 'STR',
				'filename_as_stored' => 'STR',
				'belongs_to' => 'INT',
				'guest_access' => 'INT',
				'is_locked' => 'INT',
				'broken' => 'INT',
				'created_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['uuid'],

			'autoincrement' 	=> null,

			'nullable'			=> ['belongs_to', 'guest_access', 'is_locked', 'broken', 'deleted_at'],

			'required'			=> ['uuid', 'filename', 'file_ext', 'filename_as_stored', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'uuid' => ['type' => 'str', 'max' => 60, 'required' => true],
				'filename' => ['type' => 'str', 'max' => 255, 'required' => true],
				'file_ext' => ['type' => 'str', 'max' => 30, 'required' => true],
				'filename_as_stored' => ['type' => 'str', 'max' => 60, 'required' => true],
				'belongs_to' => ['type' => 'int'],
				'guest_access' => ['type' => 'bool'],
				'is_locked' => ['type' => 'bool'],
				'broken' => ['type' => 'bool'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'deleted_at' => ['type' => 'datetime']
			],

			'fks' 				=> ['belongs_to'],

			'relationships' => [
				'users' => [
					['users.id','files.belongs_to']
				]
			],

			'expanded_relationships' => array (
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
        0 => 'files',
        1 => 'belongs_to',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users.id','files.belongs_to']
				]
			],

			'expanded_relationships_from' => array (
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
        0 => 'files',
        1 => 'belongs_to',
      ),
    ),
  ),
)
		];
	}	
}


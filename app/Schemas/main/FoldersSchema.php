<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class FoldersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'folders',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'tb', 'name', 'belongs_to', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'tb' => 'STR',
				'name' => 'STR',
				'belongs_to' => 'INT',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['tb', 'name', 'belongs_to', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'tb' => ['type' => 'str', 'max' => 50, 'required' => true],
				'name' => ['type' => 'str', 'max' => 50, 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
			],

			'fks' 				=> ['belongs_to'],

			'relationships' => [
				'users' => [
					['users.id','folders.belongs_to']
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
        0 => 'folders',
        1 => 'belongs_to',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users.id','folders.belongs_to']
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
        0 => 'folders',
        1 => 'belongs_to',
      ),
    ),
  ),
)
		];
	}	
}


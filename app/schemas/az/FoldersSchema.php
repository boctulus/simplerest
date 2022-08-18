<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FoldersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'folders',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'tb', 'name', 'belongs_to'],

			'attr_types'	=> [
				'id' => 'INT',
				'tb' => 'STR',
				'name' => 'STR',
				'belongs_to' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'required'		=> ['tb', 'name', 'belongs_to'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'tb' => ['type' => 'str', 'max' => 40, 'required' => true],
				'name' => ['type' => 'str', 'max' => 40, 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['belongs_to'],

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


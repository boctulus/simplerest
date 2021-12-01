<?php

namespace simplerest\models\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CollectionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'collections',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'entity' => 'STR',
				'refs' => 'STR',
				'belongs_to' => 'INT',
				'created_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'entity' => ['type' => 'str', 'max' => 80, 'required' => true],
				'refs' => ['type' => 'str', 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
			],

			'fks' 			=> ['belongs_to'],

			'relationships' => [
				'users' => [
					['users.id','collections.belongs_to']
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
				        0 => 'collections',
				        1 => 'belongs_to',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'users' => [
					['users.id','collections.belongs_to']
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
				        0 => 'collections',
				        1 => 'belongs_to',
				      ),
				    ),
				  ),
				)
		];
	}	
}


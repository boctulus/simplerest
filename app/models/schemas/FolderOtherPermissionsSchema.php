<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FolderOtherPermissionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'folder_other_permissions',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'folder_id' => 'INT',
				'belongs_to' => 'INT',
				'guest' => 'INT',
				'r' => 'INT',
				'w' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'guest'],

			'uniques'		=> ['folder_id'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'folder_id' => ['type' => 'int', 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'guest' => ['type' => 'bool'],
				'r' => ['type' => 'bool', 'required' => true],
				'w' => ['type' => 'bool', 'required' => true]
			],

			'fks' 			=> ['belongs_to'],

			'relationships' => [
				'users' => [
					['users.id','folder_other_permissions.belongs_to']
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
				        0 => 'folder_other_permissions',
				        1 => 'belongs_to',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'users' => [
					['users.id','folder_other_permissions.belongs_to']
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
				        0 => 'folder_other_permissions',
				        1 => 'belongs_to',
				      ),
				    ),
				  ),
				)
		];
	}	
}


<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'users',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'username', 'active', 'is_locked', 'email', 'confirmed_email', 'firstname', 'lastname', 'password', 'deleted_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'username' => 'STR',
				'active' => 'INT',
				'is_locked' => 'INT',
				'email' => 'STR',
				'confirmed_email' => 'INT',
				'firstname' => 'STR',
				'lastname' => 'STR',
				'password' => 'STR',
				'deleted_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'active', 'is_locked', 'confirmed_email', 'firstname', 'lastname', 'password', 'deleted_at'],

			'required'		=> ['username', 'email'],

			'uniques'		=> ['username', 'email'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'username' => ['type' => 'str', 'max' => 15, 'required' => true],
				'active' => ['type' => 'bool'],
				'is_locked' => ['type' => 'bool'],
				'email' => ['type' => 'str', 'max' => 60, 'required' => true],
				'confirmed_email' => ['type' => 'bool'],
				'firstname' => ['type' => 'str', 'max' => 50],
				'lastname' => ['type' => 'str', 'max' => 80],
				'password' => ['type' => 'str', 'max' => 60],
				'deleted_at' => ['type' => 'datetime']
			],

			'fks' 			=> [],

			'relationships' => [
				'api_keys' => [
					['api_keys.user_id','users.id']
				],
				'boletas' => [
					['boletas.user_id','users.id']
				],
				'books' => [
					['books.author_id','users.id'],
					['books.editor_id','users.id']
				],
				'collections' => [
					['collections.belongs_to','users.id']
				],
				'facturas' => [
					['facturas.user_id','users.id']
				],
				'facturas4' => [
					['facturas4.user_id','users.id']
				],
				'files' => [
					['files.belongs_to','users.id']
				],
				'folder_other_permissions' => [
					['folder_other_permissions.belongs_to','users.id']
				],
				'folder_permissions' => [
					['folder_permissions.belongs_to','users.id']
				],
				'folders' => [
					['folders.belongs_to','users.id']
				],
				'products' => [
					['products.belongs_to','users.id']
				],
				'user_roles' => [
					['user_roles.user_id','users.id']
				],
				'user_sp_permissions' => [
					['user_sp_permissions.user_id','users.id']
				],
				'user_tb_permissions' => [
					['user_tb_permissions.user_id','users.id']
				]
			],

			'expanded_relationships' => array (
  'api_keys' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'api_keys',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'boletas' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'boletas',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'books' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'books',
        1 => 'author_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'books',
        1 => 'editor_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'collections' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'collections',
        1 => 'belongs_to',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'facturas' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'facturas',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'facturas4' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'facturas4',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'files' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'files',
        1 => 'belongs_to',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'folder_other_permissions' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'folder_other_permissions',
        1 => 'belongs_to',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'folder_permissions' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'folder_permissions',
        1 => 'belongs_to',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'folders' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'folders',
        1 => 'belongs_to',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'products' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'products',
        1 => 'belongs_to',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'user_roles' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'user_roles',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'user_sp_permissions' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'user_sp_permissions',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'user_tb_permissions' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'user_tb_permissions',
        1 => 'user_id',
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


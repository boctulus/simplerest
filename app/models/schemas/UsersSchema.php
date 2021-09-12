<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	### TRAITS
	
	function get(){
		return [
			'table_name'	=> 'users',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'username' => 'STR',
				'active' => 'INT',
				'locked' => 'INT',
				'email' => 'STR',
				'confirmed_email' => 'INT',
				'firstname' => 'STR',
				'lastname' => 'STR',
				'password' => 'STR',
				'deleted_at' => 'STR'
			],

			'nullable'		=> ['id', 'active', 'confirmed_email', 'firstname', 'lastname', 'password', 'deleted_at'],

			'rules' 		=> [
				'username' => ['max' => 15],
				'email' => ['max' => 60],
				'firstname' => ['max' => 50],
				'lastname' => ['max' => 80],
				'password' => ['max' => 60]
			],

			'relationships' => [
				'books' => [
					['books.editor_id','users.id'],
					['books.author_id','users.id']
				],
				'user_tb_permissions' => [
					['user_tb_permissions.user_id','users.id']
				],
				'facturas4' => [
					['facturas4.user_id','users.id']
				],
				'user_roles' => [
					['user_roles.user_id','users.id']
				],
				'facturas' => [
					['facturas.user_id','users.id']
				],
				'folder_permissions' => [
					['folder_permissions.belongs_to','users.id']
				],
				'collections' => [
					['collections.belongs_to','users.id']
				],
				'boletas' => [
					['boletas.user_id','users.id']
				],
				'files' => [
					['files.belongs_to','users.id']
				],
				'api_keys' => [
					['api_keys.user_id','users.id']
				],
				'user_sp_permissions' => [
					['user_sp_permissions.user_id','users.id']
				],
				'folder_other_permissions' => [
					['folder_other_permissions.belongs_to','users.id']
				],
				'products' => [
					['products.belongs_to','users.id']
				],
				'folders' => [
					['folders.belongs_to','users.id']
				]
			]
		];
	}	
}


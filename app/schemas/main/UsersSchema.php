<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'users',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'firstname', 'lastname', 'username', 'password', 'is_active', 'is_locked', 'email', 'confirmed_email', 'address', 'belongs_to', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'firstname' => 'STR',
				'lastname' => 'STR',
				'username' => 'STR',
				'password' => 'STR',
				'is_active' => 'INT',
				'is_locked' => 'INT',
				'email' => 'STR',
				'confirmed_email' => 'INT',
				'address' => 'STR',
				'belongs_to' => 'INT',
				'created_by' => 'INT',
				'updated_by' => 'INT',
				'deleted_by' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'deleted_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'firstname', 'lastname', 'password', 'is_active', 'is_locked', 'confirmed_email', 'address', 'belongs_to', 'created_by', 'updated_by', 'deleted_by', 'updated_at', 'deleted_at'],

			'required'			=> ['username', 'email', 'created_at'],

			'uniques'			=> ['username', 'email'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'firstname' => ['type' => 'str', 'max' => 50],
				'lastname' => ['type' => 'str', 'max' => 80],
				'username' => ['type' => 'str', 'max' => 15, 'required' => true],
				'password' => ['type' => 'str', 'max' => 60],
				'is_active' => ['type' => 'bool'],
				'is_locked' => ['type' => 'bool'],
				'email' => ['type' => 'str', 'max' => 60, 'required' => true],
				'confirmed_email' => ['type' => 'bool'],
				'address' => ['type' => 'str', 'max' => 240],
				'belongs_to' => ['type' => 'int'],
				'created_by' => ['type' => 'int'],
				'updated_by' => ['type' => 'int'],
				'deleted_by' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime'],
				'deleted_at' => ['type' => 'datetime']
			],

			'fks' 				=> ['belongs_to', 'created_by', 'updated_by', 'deleted_by'],

			'relationships' => [
				'users' => [
					['users|__belongs_to.id','users.belongs_to'],
					['users|__created_by.id','users.created_by'],
					['users|__updated_by.id','users.updated_by'],
					['users|__deleted_by.id','users.deleted_by'],
					['users.belongs_to','users.id'],
					['users.created_by','users.id'],
					['users.updated_by','users.id'],
					['users.deleted_by','users.id']
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
        'alias' => '__belongs_to',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'belongs_to',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
        'alias' => '__created_by',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'created_by',
      ),
    ),
    2 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
        'alias' => '__updated_by',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'updated_by',
      ),
    ),
    3 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
        'alias' => '__deleted_by',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'deleted_by',
      ),
    ),
    4 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'belongs_to',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
    5 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'created_by',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
    6 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'updated_by',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
    7 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'deleted_by',
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
				'users' => [
					['users|__belongs_to.id','users.belongs_to'],
					['users|__created_by.id','users.created_by'],
					['users|__updated_by.id','users.updated_by'],
					['users|__deleted_by.id','users.deleted_by']
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
        'alias' => '__belongs_to',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'belongs_to',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
        'alias' => '__created_by',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'created_by',
      ),
    ),
    2 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
        'alias' => '__updated_by',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'updated_by',
      ),
    ),
    3 => 
    array (
      0 => 
      array (
        0 => 'users',
        1 => 'id',
        'alias' => '__deleted_by',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'deleted_by',
      ),
    ),
  ),
)
		];
	}	
}


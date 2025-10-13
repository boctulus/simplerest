<?php

namespace Boctulus\Simplerest\Schemas\laravelshopify;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'users',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'email', 'email_verified_at', 'password', 'remember_token', 'created_at', 'updated_at', 'shopify_grandfathered', 'shopify_namespace', 'shopify_freemium', 'plan_id', 'deleted_at', 'password_updated_at', 'theme_support_level'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR',
				'email' => 'STR',
				'email_verified_at' => 'STR',
				'password' => 'STR',
				'remember_token' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'shopify_grandfathered' => 'INT',
				'shopify_namespace' => 'STR',
				'shopify_freemium' => 'INT',
				'plan_id' => 'INT',
				'deleted_at' => 'STR',
				'password_updated_at' => 'STR',
				'theme_support_level' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'email_verified_at', 'remember_token', 'created_at', 'updated_at', 'shopify_grandfathered', 'shopify_namespace', 'shopify_freemium', 'plan_id', 'deleted_at', 'password_updated_at', 'theme_support_level'],

			'required'			=> ['name', 'email', 'password'],

			'uniques'			=> ['email'],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'name' => ['type' => 'str', 'max' => 255, 'required' => true],
				'email' => ['type' => 'str', 'max' => 255, 'required' => true],
				'email_verified_at' => ['type' => 'timestamp'],
				'password' => ['type' => 'str', 'max' => 255, 'required' => true],
				'remember_token' => ['type' => 'str', 'max' => 100],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp'],
				'shopify_grandfathered' => ['type' => 'bool'],
				'shopify_namespace' => ['type' => 'str', 'max' => 255],
				'shopify_freemium' => ['type' => 'bool'],
				'plan_id' => ['type' => 'int', 'min' => 0],
				'deleted_at' => ['type' => 'timestamp'],
				'password_updated_at' => ['type' => 'date'],
				'theme_support_level' => ['type' => 'int']
			],

			'fks' 				=> ['plan_id'],

			'relationships' => [
				'plans' => [
					['plans.id','users.plan_id']
				],
				'addresses' => [
					['addresses.user_id','users.id']
				],
				'carts' => [
					['carts.user_id','users.id']
				],
				'charges' => [
					['charges.user_id','users.id']
				],
				'favorites' => [
					['favorites.user_id','users.id']
				],
				'orders' => [
					['orders.user_id','users.id']
				]
			],

			'expanded_relationships' => array (
  'plans' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'plans',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'plan_id',
      ),
    ),
  ),
  'addresses' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'addresses',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'carts' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'carts',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'charges' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'charges',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'favorites' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'favorites',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'orders' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'orders',
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
				'plans' => [
					['plans.id','users.plan_id']
				]
			],

			'expanded_relationships_from' => array (
  'plans' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'plans',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'plan_id',
      ),
    ),
  ),
)
		];
	}	
}


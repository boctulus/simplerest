<?php

namespace Boctulus\Simplerest\Schemas\laravelshopify;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class FavoritesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'favorites',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'user_id', 'shopify_product_id', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'user_id' => 'INT',
				'shopify_product_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'created_at', 'updated_at'],

			'required'			=> ['user_id', 'shopify_product_id'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'user_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'shopify_product_id' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['user_id'],

			'relationships' => [
				'users' => [
					['users.id','favorites.user_id']
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
        0 => 'favorites',
        1 => 'user_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users.id','favorites.user_id']
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
        0 => 'favorites',
        1 => 'user_id',
      ),
    ),
  ),
)
		];
	}	
}


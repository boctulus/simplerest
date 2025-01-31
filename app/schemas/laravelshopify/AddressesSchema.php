<?php

namespace simplerest\schemas\laravelshopify;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class AddressesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'addresses',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'user_id', 'street', 'city', 'state', 'zip_code', 'is_default', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'user_id' => 'INT',
				'street' => 'STR',
				'city' => 'STR',
				'state' => 'STR',
				'zip_code' => 'STR',
				'is_default' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'is_default', 'created_at', 'updated_at'],

			'required'			=> ['user_id', 'street', 'city', 'state', 'zip_code'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'user_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'street' => ['type' => 'str', 'max' => 255, 'required' => true],
				'city' => ['type' => 'str', 'max' => 255, 'required' => true],
				'state' => ['type' => 'str', 'max' => 255, 'required' => true],
				'zip_code' => ['type' => 'str', 'max' => 255, 'required' => true],
				'is_default' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['user_id'],

			'relationships' => [
				'users' => [
					['users.id','addresses.user_id']
				],
				'orders' => [
					['orders.address_id','addresses.id']
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
        0 => 'addresses',
        1 => 'user_id',
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
        1 => 'address_id',
      ),
      1 => 
      array (
        0 => 'addresses',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users.id','addresses.user_id']
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
        0 => 'addresses',
        1 => 'user_id',
      ),
    ),
  ),
)
		];
	}	
}


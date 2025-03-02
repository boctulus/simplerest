<?php

namespace simplerest\schemas\complex01;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsersSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'users',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'email'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR',
				'email' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['name', 'email'],

			'uniques'			=> ['email'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 100, 'required' => true],
				'email' => ['type' => 'str', 'max' => 100, 'required' => true]
			],

			'fks' 				=> [],

			'relationships' => [
				'customers' => [
					['customers.user_id','users.id']
				],
				'sellers' => [
					['sellers.user_id','users.id']
				],
				'support_tickets' => [
					['support_tickets.user_id','users.id']
				]
			],

			'expanded_relationships' => array (
  'customers' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'customers',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'sellers' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'sellers',
        1 => 'user_id',
      ),
      1 => 
      array (
        0 => 'users',
        1 => 'id',
      ),
    ),
  ),
  'support_tickets' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'support_tickets',
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


<?php

namespace Boctulus\Simplerest\Schemas\complex01;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class SupportTicketsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'support_tickets',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'user_id', 'category_id', 'description', 'created_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'user_id' => 'INT',
				'category_id' => 'INT',
				'description' => 'STR',
				'created_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'created_at'],

			'required'			=> ['user_id', 'category_id', 'description'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'user_id' => ['type' => 'int', 'required' => true],
				'category_id' => ['type' => 'int', 'required' => true],
				'description' => ['type' => 'str', 'required' => true],
				'created_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['user_id'],

			'relationships' => [
				'users' => [
					['users.id','support_tickets.user_id']
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
        0 => 'support_tickets',
        1 => 'user_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'users' => [
					['users.id','support_tickets.user_id']
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
        0 => 'support_tickets',
        1 => 'user_id',
      ),
    ),
  ),
)
		];
	}	
}


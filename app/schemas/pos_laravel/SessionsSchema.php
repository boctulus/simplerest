<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class SessionsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'sessions',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity'],

			'attr_types'		=> [
				'id' => 'STR',
				'user_id' => 'INT',
				'ip_address' => 'STR',
				'user_agent' => 'STR',
				'payload' => 'STR',
				'last_activity' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> null,

			'nullable'			=> ['user_id', 'ip_address', 'user_agent'],

			'required'			=> ['id', 'payload', 'last_activity'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'str', 'max' => 255, 'required' => true],
				'user_id' => ['type' => 'int', 'min' => 0],
				'ip_address' => ['type' => 'str', 'max' => 45],
				'user_agent' => ['type' => 'str'],
				'payload' => ['type' => 'str', 'required' => true],
				'last_activity' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}


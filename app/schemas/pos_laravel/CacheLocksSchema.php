<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CacheLocksSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'cache_locks',

			'id_name'			=> 'key',

			'fields'			=> ['key', 'owner', 'expiration'],

			'attr_types'		=> [
				'key' => 'STR',
				'owner' => 'STR',
				'expiration' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['key'],

			'autoincrement' 	=> null,

			'nullable'			=> [],

			'required'			=> ['key', 'owner', 'expiration'],

			'uniques'			=> [],

			'rules' 			=> [
				'key' => ['type' => 'str', 'max' => 255, 'required' => true],
				'owner' => ['type' => 'str', 'max' => 255, 'required' => true],
				'expiration' => ['type' => 'int', 'required' => true]
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


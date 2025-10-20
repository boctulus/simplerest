<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CacheSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'cache',

			'id_name'			=> 'key',

			'fields'			=> ['key', 'value', 'expiration'],

			'attr_types'		=> [
				'key' => 'STR',
				'value' => 'STR',
				'expiration' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['key'],

			'autoincrement' 	=> null,

			'nullable'			=> [],

			'required'			=> ['key', 'value', 'expiration'],

			'uniques'			=> [],

			'rules' 			=> [
				'key' => ['type' => 'str', 'max' => 255, 'required' => true],
				'value' => ['type' => 'str', 'required' => true],
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


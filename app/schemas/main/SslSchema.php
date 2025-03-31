<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class SslSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'ssl',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'domain' => 'STR',
				'created_at' => 'STR',
				'expires_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'domain' => ['type' => 'str', 'max' => 255, 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'expires_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> [],

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


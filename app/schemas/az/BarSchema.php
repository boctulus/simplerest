<?php

namespace Boctulus\Simplerest\Schemas\az;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class BarSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'bar',

			'id_name'		=> 'uuid',

			'fields'		=> ['uuid', 'name', 'price', 'email', 'belongs_to', 'created_at', 'deleted_at', 'updated_at'],

			'attr_types'	=> [
				'uuid' => 'STR',
				'name' => 'STR',
				'price' => 'STR',
				'email' => 'STR',
				'belongs_to' => 'INT',
				'created_at' => 'STR',
				'deleted_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['uuid'],

			'autoincrement' => null,

			'nullable'		=> ['created_at', 'deleted_at', 'updated_at'],

			'required'		=> ['uuid', 'name', 'price', 'email', 'belongs_to'],

			'uniques'		=> [],

			'rules' 		=> [
				'uuid' => ['type' => 'str', 'max' => 36, 'required' => true],
				'name' => ['type' => 'str', 'max' => 50, 'required' => true],
				'price' => ['type' => 'decimal(15,2)', 'required' => true],
				'email' => ['type' => 'str', 'max' => 80, 'required' => true],
				'belongs_to' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime'],
				'deleted_at' => ['type' => 'datetime'],
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


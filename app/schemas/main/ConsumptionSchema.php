<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ConsumptionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'consumption',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'reading', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'reading' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'updated_at'],

			'required'			=> ['reading', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'reading' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
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


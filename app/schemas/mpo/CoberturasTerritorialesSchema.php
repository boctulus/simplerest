<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CoberturasTerritorialesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'coberturas_territoriales',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'territorios', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'territorios' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'required'		=> ['territorios'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'territorios' => ['type' => 'str', 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
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


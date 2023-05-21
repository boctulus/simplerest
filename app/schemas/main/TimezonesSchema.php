<?php

namespace simplerest\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TimezonesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'timezones',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'city', 'gmt'],

			'attr_types'		=> [
				'id' => 'INT',
				'city' => 'STR',
				'gmt' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['city', 'gmt'],

			'uniques'			=> ['city'],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'city' => ['type' => 'str', 'max' => 80, 'required' => true],
				'gmt' => ['type' => 'str', 'max' => 6, 'required' => true]
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


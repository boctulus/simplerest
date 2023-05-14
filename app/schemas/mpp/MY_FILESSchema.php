<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class MY_FILESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'MY_FILES',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'filename', 'filename_as_stored', 'created_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'filename' => 'STR',
				'filename_as_stored' => 'STR',
				'created_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_at'],

			'required'		=> ['filename', 'filename_as_stored'],

			'uniques'		=> ['filename_as_stored'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'filename' => ['type' => 'str', 'max' => 255, 'required' => true],
				'filename_as_stored' => ['type' => 'str', 'max' => 255, 'required' => true],
				'created_at' => ['type' => 'timestamp']
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


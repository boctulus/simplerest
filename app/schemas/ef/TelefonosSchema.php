<?php

namespace simplerest\schemas\ef;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TelefonosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'telefonos',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'nombre' => 'STR',
				'telefono' => 'STR',
				'created_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'telefono' => ['type' => 'str', 'max' => 40, 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true]
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


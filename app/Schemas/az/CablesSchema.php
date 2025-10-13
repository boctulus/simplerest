<?php

namespace Boctulus\Simplerest\Schemas\az;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CablesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'cables',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'nombre', 'calibre'],

			'attr_types'	=> [
				'id' => 'INT',
				'nombre' => 'STR',
				'calibre' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'required'		=> ['nombre', 'calibre'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'nombre' => ['type' => 'str', 'max' => 40, 'required' => true],
				'calibre' => ['type' => 'str', 'required' => true]
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


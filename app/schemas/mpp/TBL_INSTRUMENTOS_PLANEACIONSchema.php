<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_INSTRUMENTOS_PLANEACIONSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_INSTRUMENTOS_PLANEACION',

			'id_name'		=> 'ID_IPL',

			'fields'		=> ['ID_IPL', 'IPL_NOMBRE', 'IPL_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_IPL' => 'INT',
				'IPL_NOMBRE' => 'STR',
				'IPL_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_IPL'],

			'autoincrement' => 'ID_IPL',

			'nullable'		=> ['ID_IPL', 'IPL_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['IPL_NOMBRE'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_IPL' => ['type' => 'int', 'min' => 0],
				'IPL_NOMBRE' => ['type' => 'str', 'max' => 60, 'required' => true],
				'IPL_BORRADO' => ['type' => 'bool'],
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


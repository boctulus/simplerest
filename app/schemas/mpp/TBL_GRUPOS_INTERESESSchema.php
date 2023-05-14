<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_GRUPOS_INTERESESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_GRUPOS_INTERESES',

			'id_name'		=> 'ID_GRI',

			'fields'		=> ['ID_GRI', 'GRI_NOMBRE', 'GRI_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_GRI' => 'INT',
				'GRI_NOMBRE' => 'STR',
				'GRI_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_GRI'],

			'autoincrement' => 'ID_GRI',

			'nullable'		=> ['ID_GRI', 'GRI_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['GRI_NOMBRE'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_GRI' => ['type' => 'int', 'min' => 0],
				'GRI_NOMBRE' => ['type' => 'str', 'max' => 255, 'required' => true],
				'GRI_BORRADO' => ['type' => 'bool'],
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


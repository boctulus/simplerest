<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_GRUPOS_POBLACIONALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_GRUPOS_POBLACIONALES',

			'id_name'		=> 'GRU_ID',

			'fields'		=> ['GRU_ID', 'GRU_NOMBRE', 'GRU_BORRADO'],

			'attr_types'	=> [
				'GRU_ID' => 'INT',
				'GRU_NOMBRE' => 'STR',
				'GRU_BORRADO' => 'INT'
			],

			'primary'		=> ['GRU_ID'],

			'autoincrement' => 'GRU_ID',

			'nullable'		=> ['GRU_ID', 'GRU_BORRADO'],

			'required'		=> ['GRU_NOMBRE'],

			'uniques'		=> [],

			'rules' 		=> [
				'GRU_ID' => ['type' => 'int'],
				'GRU_NOMBRE' => ['type' => 'str', 'max' => 50, 'required' => true],
				'GRU_BORRADO' => ['type' => 'bool']
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


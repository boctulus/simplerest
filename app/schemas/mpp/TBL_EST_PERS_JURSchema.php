<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_EST_PERS_JURSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_EST_PERS_JUR',

			'id_name'		=> 'ID_EPJ',

			'fields'		=> ['ID_EPJ', 'EPJ_NOMBRE', 'EPJ_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_EPJ' => 'INT',
				'EPJ_NOMBRE' => 'STR',
				'EPJ_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_EPJ'],

			'autoincrement' => 'ID_EPJ',

			'nullable'		=> ['ID_EPJ', 'EPJ_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['EPJ_NOMBRE'],

			'uniques'		=> ['EPJ_NOMBRE'],

			'rules' 		=> [
				'ID_EPJ' => ['type' => 'int', 'min' => 0],
				'EPJ_NOMBRE' => ['type' => 'str', 'max' => 255, 'required' => true],
				'EPJ_BORRADO' => ['type' => 'bool'],
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


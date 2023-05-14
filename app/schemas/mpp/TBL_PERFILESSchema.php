<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PERFILESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PERFILES',

			'id_name'		=> 'PER_ID',

			'fields'		=> ['PER_ID', 'PER_NOMBRE', 'PER_BORRADO'],

			'attr_types'	=> [
				'PER_ID' => 'INT',
				'PER_NOMBRE' => 'STR',
				'PER_BORRADO' => 'INT'
			],

			'primary'		=> ['PER_ID'],

			'autoincrement' => 'PER_ID',

			'nullable'		=> ['PER_ID', 'PER_NOMBRE', 'PER_BORRADO'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'PER_ID' => ['type' => 'int'],
				'PER_NOMBRE' => ['type' => 'str', 'max' => 100],
				'PER_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_PERFILES_ROLES' => [
					['TBL_PERFILES_ROLES.PERF_ID','TBL_PERFILES.PER_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_PERFILES_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PERFILES_ROLES',
        1 => 'PERF_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PERFILES',
        1 => 'PER_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}


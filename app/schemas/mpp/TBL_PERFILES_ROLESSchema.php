<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PERFILES_ROLESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PERFILES_ROLES',

			'id_name'		=> 'PER_ID',

			'fields'		=> ['PER_ID', 'PERF_ID', 'ROL_ID'],

			'attr_types'	=> [
				'PER_ID' => 'INT',
				'PERF_ID' => 'INT',
				'ROL_ID' => 'INT'
			],

			'primary'		=> ['PER_ID'],

			'autoincrement' => 'PER_ID',

			'nullable'		=> ['PER_ID', 'PERF_ID', 'ROL_ID'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'PER_ID' => ['type' => 'int'],
				'PERF_ID' => ['type' => 'int'],
				'ROL_ID' => ['type' => 'int']
			],

			'fks' 			=> ['PERF_ID', 'ROL_ID'],

			'relationships' => [
				'TBL_PERFILES' => [
					['TBL_PERFILES.PER_ID','TBL_PERFILES_ROLES.PERF_ID']
				],
				'TBL_ROLES' => [
					['TBL_ROLES.ROL_ID','TBL_PERFILES_ROLES.ROL_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_PERFILES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PERFILES',
        1 => 'PER_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PERFILES_ROLES',
        1 => 'PERF_ID',
      ),
    ),
  ),
  'TBL_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ROLES',
        1 => 'ROL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PERFILES_ROLES',
        1 => 'ROL_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_PERFILES' => [
					['TBL_PERFILES.PER_ID','TBL_PERFILES_ROLES.PERF_ID']
				],
				'TBL_ROLES' => [
					['TBL_ROLES.ROL_ID','TBL_PERFILES_ROLES.ROL_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_PERFILES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PERFILES',
        1 => 'PER_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PERFILES_ROLES',
        1 => 'PERF_ID',
      ),
    ),
  ),
  'TBL_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ROLES',
        1 => 'ROL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PERFILES_ROLES',
        1 => 'ROL_ID',
      ),
    ),
  ),
)
		];
	}	
}


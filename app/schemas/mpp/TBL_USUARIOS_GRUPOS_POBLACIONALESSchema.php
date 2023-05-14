<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_USUARIOS_GRUPOS_POBLACIONALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_USUARIOS_GRUPOS_POBLACIONALES',

			'id_name'		=> 'GRU_ID',

			'fields'		=> ['USU_ID', 'GRU_ID'],

			'attr_types'	=> [
				'USU_ID' => 'INT',
				'GRU_ID' => 'INT'
			],

			'primary'		=> ['USU_ID', 'GRU_ID'],

			'autoincrement' => null,

			'nullable'		=> [],

			'required'		=> ['USU_ID', 'GRU_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'USU_ID' => ['type' => 'int', 'required' => true],
				'GRU_ID' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['GRU_ID', 'USU_ID'],

			'relationships' => [
				'TBL_GRUPOS_POBLACIONALES' => [
					['TBL_GRUPOS_POBLACIONALES.GRU_ID','TBL_USUARIOS_GRUPOS_POBLACIONALES.GRU_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_USUARIOS_GRUPOS_POBLACIONALES.USU_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_GRUPOS_POBLACIONALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_GRUPOS_POBLACIONALES',
        1 => 'GRU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS_GRUPOS_POBLACIONALES',
        1 => 'GRU_ID',
      ),
    ),
  ),
  'TBL_USUARIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS_GRUPOS_POBLACIONALES',
        1 => 'USU_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_GRUPOS_POBLACIONALES' => [
					['TBL_GRUPOS_POBLACIONALES.GRU_ID','TBL_USUARIOS_GRUPOS_POBLACIONALES.GRU_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_USUARIOS_GRUPOS_POBLACIONALES.USU_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_GRUPOS_POBLACIONALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_GRUPOS_POBLACIONALES',
        1 => 'GRU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS_GRUPOS_POBLACIONALES',
        1 => 'GRU_ID',
      ),
    ),
  ),
  'TBL_USUARIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS_GRUPOS_POBLACIONALES',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}


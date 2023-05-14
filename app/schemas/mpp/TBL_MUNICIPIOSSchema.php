<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_MUNICIPIOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_MUNICIPIOS',

			'id_name'		=> 'MUN_ID',

			'fields'		=> ['MUN_ID', 'MUN_NOMBRE', 'DEP_ID', 'MUN_BORRADO'],

			'attr_types'	=> [
				'MUN_ID' => 'INT',
				'MUN_NOMBRE' => 'STR',
				'DEP_ID' => 'INT',
				'MUN_BORRADO' => 'INT'
			],

			'primary'		=> ['MUN_ID'],

			'autoincrement' => 'MUN_ID',

			'nullable'		=> ['MUN_ID', 'MUN_NOMBRE', 'MUN_BORRADO'],

			'required'		=> ['DEP_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'MUN_ID' => ['type' => 'int', 'min' => 0],
				'MUN_NOMBRE' => ['type' => 'str', 'max' => 255],
				'DEP_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'MUN_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> ['DEP_ID'],

			'relationships' => [
				'TBL_DEPARTAMENTOS' => [
					['TBL_DEPARTAMENTOS.DEP_ID','TBL_MUNICIPIOS.DEP_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.MUN_ID','TBL_MUNICIPIOS.MUN_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_DEPARTAMENTOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_DEPARTAMENTOS',
        1 => 'DEP_ID',
      ),
      1 => 
      array (
        0 => 'TBL_MUNICIPIOS',
        1 => 'DEP_ID',
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
        1 => 'MUN_ID',
      ),
      1 => 
      array (
        0 => 'TBL_MUNICIPIOS',
        1 => 'MUN_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_DEPARTAMENTOS' => [
					['TBL_DEPARTAMENTOS.DEP_ID','TBL_MUNICIPIOS.DEP_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_DEPARTAMENTOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_DEPARTAMENTOS',
        1 => 'DEP_ID',
      ),
      1 => 
      array (
        0 => 'TBL_MUNICIPIOS',
        1 => 'DEP_ID',
      ),
    ),
  ),
)
		];
	}	
}


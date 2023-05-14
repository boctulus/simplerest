<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_DEPARTAMENTOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_DEPARTAMENTOS',

			'id_name'		=> 'DEP_ID',

			'fields'		=> ['DEP_ID', 'DEP_NOMBRE', 'DEP_BORRADO'],

			'attr_types'	=> [
				'DEP_ID' => 'INT',
				'DEP_NOMBRE' => 'STR',
				'DEP_BORRADO' => 'INT'
			],

			'primary'		=> ['DEP_ID'],

			'autoincrement' => null,

			'nullable'		=> ['DEP_NOMBRE', 'DEP_BORRADO'],

			'required'		=> ['DEP_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'DEP_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'DEP_NOMBRE' => ['type' => 'str', 'max' => 255],
				'DEP_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_MUNICIPIOS' => [
					['TBL_MUNICIPIOS.DEP_ID','TBL_DEPARTAMENTOS.DEP_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.DEP_ID','TBL_DEPARTAMENTOS.DEP_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_MUNICIPIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_MUNICIPIOS',
        1 => 'DEP_ID',
      ),
      1 => 
      array (
        0 => 'TBL_DEPARTAMENTOS',
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
        1 => 'DEP_ID',
      ),
      1 => 
      array (
        0 => 'TBL_DEPARTAMENTOS',
        1 => 'DEP_ID',
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


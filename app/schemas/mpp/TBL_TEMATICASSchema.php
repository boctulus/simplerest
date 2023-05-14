<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_TEMATICASSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_TEMATICAS',

			'id_name'		=> 'TEM_ID',

			'fields'		=> ['TEM_ID', 'TEM_DESCRIPCION', 'TEM_BORRADO', 'USU_ID'],

			'attr_types'	=> [
				'TEM_ID' => 'INT',
				'TEM_DESCRIPCION' => 'STR',
				'TEM_BORRADO' => 'INT',
				'USU_ID' => 'INT'
			],

			'primary'		=> ['TEM_ID'],

			'autoincrement' => 'TEM_ID',

			'nullable'		=> ['TEM_ID', 'TEM_BORRADO'],

			'required'		=> ['TEM_DESCRIPCION', 'USU_ID'],

			'uniques'		=> ['TEM_DESCRIPCION'],

			'rules' 		=> [
				'TEM_ID' => ['type' => 'int'],
				'TEM_DESCRIPCION' => ['type' => 'str', 'max' => 200, 'required' => true],
				'TEM_BORRADO' => ['type' => 'bool'],
				'USU_ID' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['USU_ID'],

			'relationships' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_TEMATICAS.USU_ID']
				],
				'TBL_ELECCIONES' => [
					['TBL_ELECCIONES.TEM_ID','TBL_TEMATICAS.TEM_ID']
				]
			],

			'expanded_relationships' => array (
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
        0 => 'TBL_TEMATICAS',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_ELECCIONES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ELECCIONES',
        1 => 'TEM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_TEMATICAS',
        1 => 'TEM_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_TEMATICAS.USU_ID']
				]
			],

			'expanded_relationships_from' => array (
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
        0 => 'TBL_TEMATICAS',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}


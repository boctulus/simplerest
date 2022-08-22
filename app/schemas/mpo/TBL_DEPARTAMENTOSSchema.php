<?php

namespace simplerest\schemas\mpo;

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

			'autoincrement' => 'DEP_ID',

			'nullable'		=> ['DEP_ID', 'DEP_BORRADO'],

			'required'		=> ['DEP_NOMBRE'],

			'uniques'		=> ['DEP_NOMBRE'],

			'rules' 		=> [
				'DEP_ID' => ['type' => 'int', 'min' => 0],
				'DEP_NOMBRE' => ['type' => 'str', 'max' => 60, 'required' => true],
				'DEP_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_REPRESENTANTES_LEGALES' => [
					['TBL_REPRESENTANTES_LEGALES.DEPARTAMENTO_EXP_ID','TBL_DEPARTAMENTOS.DEP_ID']
				],
				'TBL_MUNICIPIOS' => [
					['TBL_MUNICIPIOS.DEP_ID','TBL_DEPARTAMENTOS.DEP_ID']
				],
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.DEPARTAMENTO_ID','TBL_DEPARTAMENTOS.DEP_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_REPRESENTANTES_LEGALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_REPRESENTANTES_LEGALES',
        1 => 'DEPARTAMENTO_EXP_ID',
      ),
      1 => 
      array (
        0 => 'TBL_DEPARTAMENTOS',
        1 => 'DEP_ID',
      ),
    ),
  ),
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
  'TBL_ORG_COMUNALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNALES',
        1 => 'DEPARTAMENTO_ID',
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


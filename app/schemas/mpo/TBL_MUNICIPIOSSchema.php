<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_MUNICIPIOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_MUNICIPIOS',

			'id_name'		=> 'MUN_ID',

			'fields'		=> ['MUN_ID', 'MUN_NOMBRE', 'FK_DEP_ID', 'MUN_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'MUN_ID' => 'INT',
				'MUN_NOMBRE' => 'STR',
				'FK_DEP_ID' => 'INT',
				'MUN_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['MUN_ID'],

			'autoincrement' => 'MUN_ID',

			'nullable'		=> ['MUN_ID', 'MUN_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['MUN_NOMBRE', 'FK_DEP_ID'],

			'uniques'		=> ['MUN_NOMBRE'],

			'rules' 		=> [
				'MUN_ID' => ['type' => 'int', 'min' => 0],
				'MUN_NOMBRE' => ['type' => 'str', 'max' => 60, 'required' => true],
				'FK_DEP_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'MUN_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> ['FK_DEP_ID'],

			'relationships' => [
				'TBL_DEPARTAMENTOS' => [
					['TBL_DEPARTAMENTOS.DEP_ID','TBL_MUNICIPIOS.FK_DEP_ID']
				],
				'TBL_REPRESENTANTES_LEGALES' => [
					['TBL_REPRESENTANTES_LEGALES.MUNICIPIO_EXP_ID','TBL_MUNICIPIOS.MUN_ID']
				],
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.MUNICIPIO_ID','TBL_MUNICIPIOS.MUN_ID']
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
        1 => 'FK_DEP_ID',
      ),
    ),
  ),
  'TBL_REPRESENTANTES_LEGALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_REPRESENTANTES_LEGALES',
        1 => 'MUNICIPIO_EXP_ID',
      ),
      1 => 
      array (
        0 => 'TBL_MUNICIPIOS',
        1 => 'MUN_ID',
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
        1 => 'MUNICIPIO_ID',
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
					['TBL_DEPARTAMENTOS.DEP_ID','TBL_MUNICIPIOS.FK_DEP_ID']
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
        1 => 'FK_DEP_ID',
      ),
    ),
  ),
)
		];
	}	
}


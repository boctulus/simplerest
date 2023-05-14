<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_LOGSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_LOGS',

			'id_name'		=> 'LOG_ID',

			'fields'		=> ['LOG_ID', 'LOG_NOMBRE_PROCESO', 'LOG_DETALLE', 'LOG_BORRADO', 'USU_ID'],

			'attr_types'	=> [
				'LOG_ID' => 'INT',
				'LOG_NOMBRE_PROCESO' => 'STR',
				'LOG_DETALLE' => 'STR',
				'LOG_BORRADO' => 'INT',
				'USU_ID' => 'INT'
			],

			'primary'		=> ['LOG_ID'],

			'autoincrement' => 'LOG_ID',

			'nullable'		=> ['LOG_ID', 'LOG_BORRADO'],

			'required'		=> ['LOG_NOMBRE_PROCESO', 'LOG_DETALLE', 'USU_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'LOG_ID' => ['type' => 'int'],
				'LOG_NOMBRE_PROCESO' => ['type' => 'str', 'max' => 100, 'required' => true],
				'LOG_DETALLE' => ['type' => 'str', 'max' => 200, 'required' => true],
				'LOG_BORRADO' => ['type' => 'bool'],
				'USU_ID' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['USU_ID'],

			'relationships' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_LOGS.USU_ID']
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
        0 => 'TBL_LOGS',
        1 => 'USU_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_LOGS.USU_ID']
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
        0 => 'TBL_LOGS',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}


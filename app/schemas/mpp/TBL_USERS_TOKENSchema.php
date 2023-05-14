<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_USERS_TOKENSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_USERS_TOKEN',

			'id_name'		=> 'USE_ID',

			'fields'		=> ['USE_ID', 'USU_ID', 'USE_TOKEN', 'USE_FECHA', 'USE_IP', 'USE_ESTADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'USE_ID' => 'INT',
				'USU_ID' => 'INT',
				'USE_TOKEN' => 'STR',
				'USE_FECHA' => 'STR',
				'USE_IP' => 'STR',
				'USE_ESTADO' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['USE_ID'],

			'autoincrement' => 'USE_ID',

			'nullable'		=> ['USE_ID', 'USE_IP', 'USE_ESTADO', 'created_at', 'updated_at'],

			'required'		=> ['USU_ID', 'USE_TOKEN', 'USE_FECHA'],

			'uniques'		=> [],

			'rules' 		=> [
				'USE_ID' => ['type' => 'int'],
				'USU_ID' => ['type' => 'int', 'required' => true],
				'USE_TOKEN' => ['type' => 'str', 'required' => true],
				'USE_FECHA' => ['type' => 'datetime', 'required' => true],
				'USE_IP' => ['type' => 'str', 'max' => 50],
				'USE_ESTADO' => ['type' => 'str'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> ['USU_ID'],

			'relationships' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_USERS_TOKEN.USU_ID']
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
        0 => 'TBL_USERS_TOKEN',
        1 => 'USU_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_USERS_TOKEN.USU_ID']
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
        0 => 'TBL_USERS_TOKEN',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}


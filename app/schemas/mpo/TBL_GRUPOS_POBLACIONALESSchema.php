<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_GRUPOS_POBLACIONALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_GRUPOS_POBLACIONALES',

			'id_name'		=> 'GRU_ID',

			'fields'		=> ['GRU_ID', 'GRU_NOMBRE', 'GRU_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'GRU_ID' => 'INT',
				'GRU_NOMBRE' => 'STR',
				'GRU_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['GRU_ID'],

			'autoincrement' => 'GRU_ID',

			'nullable'		=> ['GRU_ID', 'GRU_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['GRU_NOMBRE'],

			'uniques'		=> ['GRU_NOMBRE'],

			'rules' 		=> [
				'GRU_ID' => ['type' => 'int', 'min' => 0],
				'GRU_NOMBRE' => ['type' => 'str', 'max' => 20, 'required' => true],
				'GRU_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ENTIDADES_REG' => [
					['TBL_ENTIDADES_REG.ERG_GRUPO_POBLACIONAL_ID','TBL_GRUPOS_POBLACIONALES.GRU_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_ENTIDADES_REG' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ENTIDADES_REG',
        1 => 'ERG_GRUPO_POBLACIONAL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_GRUPOS_POBLACIONALES',
        1 => 'GRU_ID',
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


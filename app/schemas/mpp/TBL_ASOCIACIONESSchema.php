<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ASOCIACIONESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ASOCIACIONES',

			'id_name'		=> 'ASO_ID',

			'fields'		=> ['ASO_ID', 'ASO_NOMBRE', 'ASO_BORRADO'],

			'attr_types'	=> [
				'ASO_ID' => 'INT',
				'ASO_NOMBRE' => 'STR',
				'ASO_BORRADO' => 'INT'
			],

			'primary'		=> ['ASO_ID'],

			'autoincrement' => null,

			'nullable'		=> ['ASO_ID', 'ASO_BORRADO'],

			'required'		=> ['ASO_NOMBRE'],

			'uniques'		=> [],

			'rules' 		=> [
				'ASO_ID' => ['type' => 'int'],
				'ASO_NOMBRE' => ['type' => 'str', 'max' => 250, 'required' => true],
				'ASO_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.ASO_ID','TBL_ASOCIACIONES.ASO_ID']
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
        1 => 'ASO_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ASOCIACIONES',
        1 => 'ASO_ID',
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


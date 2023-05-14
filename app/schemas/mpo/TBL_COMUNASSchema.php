<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_COMUNASSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_COMUNAS',

			'id_name'		=> 'COM_ID',

			'fields'		=> ['COM_ID', 'COM_ZONA', 'COM_NUMERO', 'COM_NOMBRE', 'COM_BORRADO'],

			'attr_types'	=> [
				'COM_ID' => 'INT',
				'COM_ZONA' => 'STR',
				'COM_NUMERO' => 'STR',
				'COM_NOMBRE' => 'STR',
				'COM_BORRADO' => 'INT'
			],

			'primary'		=> ['COM_ID'],

			'autoincrement' => 'COM_ID',

			'nullable'		=> ['COM_ID', 'COM_BORRADO'],

			'required'		=> ['COM_ZONA', 'COM_NUMERO', 'COM_NOMBRE'],

			'uniques'		=> ['COM_NUMERO'],

			'rules' 		=> [
				'COM_ID' => ['type' => 'int', 'min' => 0],
				'COM_ZONA' => ['type' => 'str', 'max' => 255, 'required' => true],
				'COM_NUMERO' => ['type' => 'str', 'max' => 255, 'required' => true],
				'COM_NOMBRE' => ['type' => 'str', 'max' => 255, 'required' => true],
				'COM_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_BARRIOS' => [
					['TBL_BARRIOS.COM_ID','TBL_COMUNAS.COM_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_BARRIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_BARRIOS',
        1 => 'COM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_COMUNAS',
        1 => 'COM_ID',
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


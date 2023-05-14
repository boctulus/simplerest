<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_BARRIOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_BARRIOS',

			'id_name'		=> 'BAR_ID',

			'fields'		=> ['BAR_ID', 'BAR_NOMBRE', 'BAR_BORRADO', 'COM_ID'],

			'attr_types'	=> [
				'BAR_ID' => 'INT',
				'BAR_NOMBRE' => 'STR',
				'BAR_BORRADO' => 'INT',
				'COM_ID' => 'INT'
			],

			'primary'		=> ['BAR_ID'],

			'autoincrement' => 'BAR_ID',

			'nullable'		=> ['BAR_ID', 'BAR_BORRADO'],

			'required'		=> ['BAR_NOMBRE', 'COM_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'BAR_ID' => ['type' => 'int'],
				'BAR_NOMBRE' => ['type' => 'str', 'max' => 200, 'required' => true],
				'BAR_BORRADO' => ['type' => 'bool'],
				'COM_ID' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['COM_ID'],

			'relationships' => [
				'TBL_COMUNAS' => [
					['TBL_COMUNAS.COM_ID','TBL_BARRIOS.COM_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.BAR_ID','TBL_BARRIOS.BAR_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_COMUNAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_COMUNAS',
        1 => 'COM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_BARRIOS',
        1 => 'COM_ID',
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
        1 => 'BAR_ID',
      ),
      1 => 
      array (
        0 => 'TBL_BARRIOS',
        1 => 'BAR_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_COMUNAS' => [
					['TBL_COMUNAS.COM_ID','TBL_BARRIOS.COM_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_COMUNAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_COMUNAS',
        1 => 'COM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_BARRIOS',
        1 => 'COM_ID',
      ),
    ),
  ),
)
		];
	}	
}


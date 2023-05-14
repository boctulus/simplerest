<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_BARRIOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_BARRIOS',

			'id_name'		=> 'BAR_ID',

			'fields'		=> ['BAR_ID', 'BAR_NOMBRE', 'COM_ID', 'BAR_BORRADO'],

			'attr_types'	=> [
				'BAR_ID' => 'INT',
				'BAR_NOMBRE' => 'STR',
				'COM_ID' => 'INT',
				'BAR_BORRADO' => 'INT'
			],

			'primary'		=> ['BAR_ID'],

			'autoincrement' => 'BAR_ID',

			'nullable'		=> ['BAR_ID', 'BAR_BORRADO'],

			'required'		=> ['BAR_NOMBRE', 'COM_ID'],

			'uniques'		=> ['BAR_NOMBRE'],

			'rules' 		=> [
				'BAR_ID' => ['type' => 'int', 'min' => 0],
				'BAR_NOMBRE' => ['type' => 'str', 'max' => 255, 'required' => true],
				'COM_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'BAR_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> ['COM_ID'],

			'relationships' => [
				'TBL_COMUNAS' => [
					['TBL_COMUNAS.COM_ID','TBL_BARRIOS.COM_ID']
				],
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.BARRIO_ID','TBL_BARRIOS.BAR_ID']
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
  'TBL_ORG_COMUNALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNALES',
        1 => 'BARRIO_ID',
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


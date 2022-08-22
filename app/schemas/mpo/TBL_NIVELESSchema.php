<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_NIVELESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_NIVELES',

			'id_name'		=> 'ID_NIV',

			'fields'		=> ['ID_NIV', 'NIV_NOMBRE', 'NIV_BORRADO'],

			'attr_types'	=> [
				'ID_NIV' => 'INT',
				'NIV_NOMBRE' => 'STR',
				'NIV_BORRADO' => 'INT'
			],

			'primary'		=> ['ID_NIV'],

			'autoincrement' => 'ID_NIV',

			'nullable'		=> ['ID_NIV', 'NIV_BORRADO'],

			'required'		=> ['NIV_NOMBRE'],

			'uniques'		=> ['NIV_NOMBRE'],

			'rules' 		=> [
				'ID_NIV' => ['type' => 'int', 'min' => 0],
				'NIV_NOMBRE' => ['type' => 'str', 'max' => 30, 'required' => true],
				'NIV_BORRADO' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.NIVEL_ID','TBL_NIVELES.ID_NIV']
				]
			],

			'expanded_relationships' => array (
  'TBL_ORG_COMUNALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNALES',
        1 => 'NIVEL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_NIVELES',
        1 => 'ID_NIV',
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


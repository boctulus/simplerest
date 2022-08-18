<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ESTADOS_CIVILESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ESTADOS_CIVILES',

			'id_name'		=> 'ID_ECV',

			'fields'		=> ['ID_ECV', 'ECV_NOMBRE', 'ECV_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_ECV' => 'INT',
				'ECV_NOMBRE' => 'STR',
				'ECV_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_ECV'],

			'autoincrement' => 'ID_ECV',

			'nullable'		=> ['ID_ECV', 'ECV_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['ECV_NOMBRE'],

			'uniques'		=> ['ECV_NOMBRE'],

			'rules' 		=> [
				'ID_ECV' => ['type' => 'int', 'min' => 0],
				'ECV_NOMBRE' => ['type' => 'str', 'max' => 20, 'required' => true],
				'ECV_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_REPRESENTANTES_LEGALES' => [
					['TBL_REPRESENTANTES_LEGALES.ESTADO_CIVIL_ID','TBL_ESTADOS_CIVILES.ID_ECV']
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
        1 => 'ESTADO_CIVIL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ESTADOS_CIVILES',
        1 => 'ID_ECV',
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


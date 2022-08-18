<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ESCALAS_TERRITORIALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ESCALAS_TERRITORIALES',

			'id_name'		=> 'ID_ETR',

			'fields'		=> ['ID_ETR', 'ETR_NOMBRE', 'ETR_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_ETR' => 'INT',
				'ETR_NOMBRE' => 'STR',
				'ETR_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_ETR'],

			'autoincrement' => 'ID_ETR',

			'nullable'		=> ['ID_ETR', 'ETR_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['ETR_NOMBRE'],

			'uniques'		=> ['ETR_NOMBRE'],

			'rules' 		=> [
				'ID_ETR' => ['type' => 'int', 'min' => 0],
				'ETR_NOMBRE' => ['type' => 'str', 'max' => 40, 'required' => true],
				'ETR_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.ESCALA_TERRITORIAL_ID','TBL_ESCALAS_TERRITORIALES.ID_ETR']
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
        1 => 'ESCALA_TERRITORIAL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ESCALAS_TERRITORIALES',
        1 => 'ID_ETR',
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


<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_SUBREGIONESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_SUBREGIONES',

			'id_name'		=> 'ID_SBR',

			'fields'		=> ['ID_SBR', 'SBR_nombre', 'SBR_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_SBR' => 'INT',
				'SBR_nombre' => 'STR',
				'SBR_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_SBR'],

			'autoincrement' => 'ID_SBR',

			'nullable'		=> ['ID_SBR', 'SBR_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['SBR_nombre'],

			'uniques'		=> ['SBR_nombre'],

			'rules' 		=> [
				'ID_SBR' => ['type' => 'int', 'min' => 0],
				'SBR_nombre' => ['type' => 'str', 'max' => 255, 'required' => true],
				'SBR_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.SUBREGION_ID','TBL_SUBREGIONES.ID_SBR']
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
        1 => 'SUBREGION_ID',
      ),
      1 => 
      array (
        0 => 'TBL_SUBREGIONES',
        1 => 'ID_SBR',
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

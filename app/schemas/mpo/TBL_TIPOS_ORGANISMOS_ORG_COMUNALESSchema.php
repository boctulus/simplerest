<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_TIPOS_ORGANISMOS_ORG_COMUNALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_TIPOS_ORGANISMOS_ORG_COMUNALES',

			'id_name'		=> 'ID_TOC',

			'fields'		=> ['ID_TOC', 'TOC_NOMBRE', 'TOC_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_TOC' => 'INT',
				'TOC_NOMBRE' => 'STR',
				'TOC_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_TOC'],

			'autoincrement' => 'ID_TOC',

			'nullable'		=> ['ID_TOC', 'TOC_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['TOC_NOMBRE'],

			'uniques'		=> ['TOC_NOMBRE'],

			'rules' 		=> [
				'ID_TOC' => ['type' => 'int', 'min' => 0],
				'TOC_NOMBRE' => ['type' => 'str', 'max' => 50, 'required' => true],
				'TOC_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.TIPO_ORGANISMO_ORG_COMUNAL_ID','TBL_TIPOS_ORGANISMOS_ORG_COMUNALES.ID_TOC']
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
        1 => 'TIPO_ORGANISMO_ORG_COMUNAL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_TIPOS_ORGANISMOS_ORG_COMUNALES',
        1 => 'ID_TOC',
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

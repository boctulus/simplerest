<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_TIPOS_DOCSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_TIPOS_DOC',

			'id_name'		=> 'ID_TDC',

			'fields'		=> ['ID_TDC', 'TDC_NOMBRE', 'TDC_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_TDC' => 'INT',
				'TDC_NOMBRE' => 'STR',
				'TDC_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_TDC'],

			'autoincrement' => 'ID_TDC',

			'nullable'		=> ['ID_TDC', 'TDC_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['TDC_NOMBRE'],

			'uniques'		=> ['TDC_NOMBRE'],

			'rules' 		=> [
				'ID_TDC' => ['type' => 'int', 'min' => 0],
				'TDC_NOMBRE' => ['type' => 'str', 'max' => 40, 'required' => true],
				'TDC_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_REPRESENTANTES_LEGALES' => [
					['TBL_REPRESENTANTES_LEGALES.TIPO_DOC_ID','TBL_TIPOS_DOC.ID_TDC']
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
        1 => 'TIPO_DOC_ID',
      ),
      1 => 
      array (
        0 => 'TBL_TIPOS_DOC',
        1 => 'ID_TDC',
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


<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ESTADOS_SEGUIMIENTOSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ESTADOS_SEGUIMIENTO',

			'id_name'		=> 'ID_ESG',

			'fields'		=> ['ID_ESG', 'ESG_NOMBRE', 'ESG_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_ESG' => 'INT',
				'ESG_NOMBRE' => 'STR',
				'ESG_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_ESG'],

			'autoincrement' => 'ID_ESG',

			'nullable'		=> ['ID_ESG', 'ESG_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['ESG_NOMBRE'],

			'uniques'		=> ['ESG_NOMBRE'],

			'rules' 		=> [
				'ID_ESG' => ['type' => 'int', 'min' => 0],
				'ESG_NOMBRE' => ['type' => 'str', 'max' => 20, 'required' => true],
				'ESG_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.ESTADO_SEGUIMIENTO_ID','TBL_ESTADOS_SEGUIMIENTO.ID_ESG']
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
        1 => 'ESTADO_SEGUIMIENTO_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ESTADOS_SEGUIMIENTO',
        1 => 'ID_ESG',
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


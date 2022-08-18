<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_CERTIFICACIONES_QUE_EMITE_ORG_COMUNALSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_CERTIFICACIONES_QUE_EMITE_ORG_COMUNAL',

			'id_name'		=> 'ID_COC',

			'fields'		=> ['ID_COC', 'COC_NOMBRE', 'COC_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_COC' => 'INT',
				'COC_NOMBRE' => 'STR',
				'COC_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_COC'],

			'autoincrement' => 'ID_COC',

			'nullable'		=> ['ID_COC', 'COC_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['COC_NOMBRE'],

			'uniques'		=> ['COC_NOMBRE'],

			'rules' 		=> [
				'ID_COC' => ['type' => 'int', 'min' => 0],
				'COC_NOMBRE' => ['type' => 'str', 'max' => 60, 'required' => true],
				'COC_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.CERTIFICACION_QUE_EMITE_ORG_COMUNAL_ID','TBL_CERTIFICACIONES_QUE_EMITE_ORG_COMUNAL.ID_COC']
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
        1 => 'CERTIFICACION_QUE_EMITE_ORG_COMUNAL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_CERTIFICACIONES_QUE_EMITE_ORG_COMUNAL',
        1 => 'ID_COC',
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


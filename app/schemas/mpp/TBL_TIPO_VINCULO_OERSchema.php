<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_TIPO_VINCULO_OERSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_TIPO_VINCULO_OER',

			'id_name'		=> 'ID_TVO',

			'fields'		=> ['ID_TVO', 'TVO_NOMBRE', 'TVO_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_TVO' => 'INT',
				'TVO_NOMBRE' => 'STR',
				'TVO_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_TVO'],

			'autoincrement' => 'ID_TVO',

			'nullable'		=> ['ID_TVO', 'TVO_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['TVO_NOMBRE'],

			'uniques'		=> ['TVO_NOMBRE'],

			'rules' 		=> [
				'ID_TVO' => ['type' => 'int', 'min' => 0],
				'TVO_NOMBRE' => ['type' => 'str', 'max' => 30, 'required' => true],
				'TVO_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNAL_ENTIDAD_REG' => [
					['TBL_ORG_COMUNAL_ENTIDAD_REG.TIPO_VINCULO_ID','TBL_TIPO_VINCULO_OER.ID_TVO']
				]
			],

			'expanded_relationships' => array (
  'TBL_ORG_COMUNAL_ENTIDAD_REG' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
        1 => 'TIPO_VINCULO_ID',
      ),
      1 => 
      array (
        0 => 'TBL_TIPO_VINCULO_OER',
        1 => 'ID_TVO',
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


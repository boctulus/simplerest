<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_INSTRUMENTOS_PLANEACIONSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_INSTRUMENTOS_PLANEACION',

			'id_name'		=> 'ID_IPL',

			'fields'		=> ['ID_IPL', 'IPL_NOMBRE', 'IPL_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_IPL' => 'INT',
				'IPL_NOMBRE' => 'STR',
				'IPL_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_IPL'],

			'autoincrement' => 'ID_IPL',

			'nullable'		=> ['ID_IPL', 'IPL_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['IPL_NOMBRE'],

			'uniques'		=> ['IPL_NOMBRE'],

			'rules' 		=> [
				'ID_IPL' => ['type' => 'int', 'min' => 0],
				'IPL_NOMBRE' => ['type' => 'str', 'max' => 60, 'required' => true],
				'IPL_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.INSTRUMENTO_PLANEACION_ID','TBL_INSTRUMENTOS_PLANEACION.ID_IPL']
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
        1 => 'INSTRUMENTO_PLANEACION_ID',
      ),
      1 => 
      array (
        0 => 'TBL_INSTRUMENTOS_PLANEACION',
        1 => 'ID_IPL',
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


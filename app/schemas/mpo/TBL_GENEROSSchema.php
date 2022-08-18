<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_GENEROSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_GENEROS',

			'id_name'		=> 'ID_GEN',

			'fields'		=> ['ID_GEN', 'GEN_NOMBRE', 'GEN_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_GEN' => 'INT',
				'GEN_NOMBRE' => 'STR',
				'GEN_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_GEN'],

			'autoincrement' => 'ID_GEN',

			'nullable'		=> ['ID_GEN', 'GEN_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['GEN_NOMBRE'],

			'uniques'		=> ['GEN_NOMBRE'],

			'rules' 		=> [
				'ID_GEN' => ['type' => 'int', 'min' => 0],
				'GEN_NOMBRE' => ['type' => 'str', 'max' => 30, 'required' => true],
				'GEN_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_REPRESENTANTES_LEGALES' => [
					['TBL_REPRESENTANTES_LEGALES.GENERO_ID','TBL_GENEROS.ID_GEN']
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
        1 => 'GENERO_ID',
      ),
      1 => 
      array (
        0 => 'TBL_GENEROS',
        1 => 'ID_GEN',
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


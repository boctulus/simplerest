<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_NIVELES_ESCOLARIDADSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_NIVELES_ESCOLARIDAD',

			'id_name'		=> 'ID_NVE',

			'fields'		=> ['ID_NVE', 'NVE_NOMBRE', 'NVE_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_NVE' => 'INT',
				'NVE_NOMBRE' => 'STR',
				'NVE_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_NVE'],

			'autoincrement' => 'ID_NVE',

			'nullable'		=> ['ID_NVE', 'NVE_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['NVE_NOMBRE'],

			'uniques'		=> ['NVE_NOMBRE'],

			'rules' 		=> [
				'ID_NVE' => ['type' => 'int', 'min' => 0],
				'NVE_NOMBRE' => ['type' => 'str', 'max' => 20, 'required' => true],
				'NVE_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_REPRESENTANTES_LEGALES' => [
					['TBL_REPRESENTANTES_LEGALES.NIVEL_ESCOLARIDAD_ID','TBL_NIVELES_ESCOLARIDAD.ID_NVE']
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
        1 => 'NIVEL_ESCOLARIDAD_ID',
      ),
      1 => 
      array (
        0 => 'TBL_NIVELES_ESCOLARIDAD',
        1 => 'ID_NVE',
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


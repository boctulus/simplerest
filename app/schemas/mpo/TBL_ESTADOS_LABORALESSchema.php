<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ESTADOS_LABORALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ESTADOS_LABORALES',

			'id_name'		=> 'ID_ELA',

			'fields'		=> ['ID_ELA', 'ELA_NOMBRE', 'ELA_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_ELA' => 'INT',
				'ELA_NOMBRE' => 'STR',
				'ELA_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_ELA'],

			'autoincrement' => 'ID_ELA',

			'nullable'		=> ['ID_ELA', 'ELA_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['ELA_NOMBRE'],

			'uniques'		=> ['ELA_NOMBRE'],

			'rules' 		=> [
				'ID_ELA' => ['type' => 'int', 'min' => 0],
				'ELA_NOMBRE' => ['type' => 'str', 'max' => 20, 'required' => true],
				'ELA_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_REPRESENTANTES_LEGALES' => [
					['TBL_REPRESENTANTES_LEGALES.ESTADO_LABORAL_ID','TBL_ESTADOS_LABORALES.ID_ELA']
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
        1 => 'ESTADO_LABORAL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ESTADOS_LABORALES',
        1 => 'ID_ELA',
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

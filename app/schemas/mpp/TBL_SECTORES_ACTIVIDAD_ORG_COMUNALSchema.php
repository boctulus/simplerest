<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_SECTORES_ACTIVIDAD_ORG_COMUNALSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_SECTORES_ACTIVIDAD_ORG_COMUNAL',

			'id_name'		=> 'ID_SAO',

			'fields'		=> ['ID_SAO', 'SAO_NOMBRE', 'SAO_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_SAO' => 'INT',
				'SAO_NOMBRE' => 'STR',
				'SAO_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_SAO'],

			'autoincrement' => 'ID_SAO',

			'nullable'		=> ['ID_SAO', 'SAO_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['SAO_NOMBRE'],

			'uniques'		=> ['SAO_NOMBRE'],

			'rules' 		=> [
				'ID_SAO' => ['type' => 'int', 'min' => 0],
				'SAO_NOMBRE' => ['type' => 'str', 'max' => 40, 'required' => true],
				'SAO_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}


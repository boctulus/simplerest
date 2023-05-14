<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ASISTENCIA_RENDICIONSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ASISTENCIA_RENDICION',

			'id_name'		=> 'ASI_ID',

			'fields'		=> ['ASI_ID', 'ASI_HOMBRES', 'ASI_MUJERES', 'ASI_PRIMERA_INFANCIA', 'ASI_NIÑEZ_ADOSLECENCIA', 'ASI_JUVENTUD', 'ASI_ADULTOS', 'ASI_ADULTO_MAYOR', 'ASI_ENFOQUE_POBLACIONAL', 'REN_ID', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ASI_ID' => 'INT',
				'ASI_HOMBRES' => 'INT',
				'ASI_MUJERES' => 'INT',
				'ASI_PRIMERA_INFANCIA' => 'INT',
				'ASI_NIÑEZ_ADOSLECENCIA' => 'INT',
				'ASI_JUVENTUD' => 'INT',
				'ASI_ADULTOS' => 'INT',
				'ASI_ADULTO_MAYOR' => 'INT',
				'ASI_ENFOQUE_POBLACIONAL' => 'STR',
				'REN_ID' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ASI_ID'],

			'autoincrement' => 'ASI_ID',

			'nullable'		=> ['ASI_ID', 'ASI_HOMBRES', 'ASI_MUJERES', 'ASI_PRIMERA_INFANCIA', 'ASI_NIÑEZ_ADOSLECENCIA', 'ASI_JUVENTUD', 'ASI_ADULTOS', 'ASI_ADULTO_MAYOR', 'ASI_ENFOQUE_POBLACIONAL', 'REN_ID', 'created_at', 'updated_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'ASI_ID' => ['type' => 'int'],
				'ASI_HOMBRES' => ['type' => 'int'],
				'ASI_MUJERES' => ['type' => 'int'],
				'ASI_PRIMERA_INFANCIA' => ['type' => 'int'],
				'ASI_NIÑEZ_ADOSLECENCIA' => ['type' => 'int'],
				'ASI_JUVENTUD' => ['type' => 'int'],
				'ASI_ADULTOS' => ['type' => 'int'],
				'ASI_ADULTO_MAYOR' => ['type' => 'int'],
				'ASI_ENFOQUE_POBLACIONAL' => ['type' => 'str'],
				'REN_ID' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
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


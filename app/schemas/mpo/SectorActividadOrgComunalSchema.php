<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class SectorActividadOrgComunalSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'sector_actividad_org_comunal',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'nombre' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'uniques'		=> ['nombre'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'nombre' => ['type' => 'str', 'max' => 40, 'required' => true],
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


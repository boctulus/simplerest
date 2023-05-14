<?php

namespace simplerest\schemas\mpp;

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


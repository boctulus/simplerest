<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_RESULTADOS_PROCESOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_RESULTADOS_PROCESOS',

			'id_name'		=> 'RES_ID',

			'fields'		=> ['RES_ID', 'PRO_ID', 'RES_DATOS', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'RES_ID' => 'INT',
				'PRO_ID' => 'INT',
				'RES_DATOS' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['RES_ID'],

			'autoincrement' => 'RES_ID',

			'nullable'		=> ['RES_ID', 'PRO_ID', 'RES_DATOS', 'created_at', 'updated_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'RES_ID' => ['type' => 'int'],
				'PRO_ID' => ['type' => 'int'],
				'RES_DATOS' => ['type' => 'str'],
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


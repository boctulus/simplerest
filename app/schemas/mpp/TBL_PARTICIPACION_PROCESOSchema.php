<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PARTICIPACION_PROCESOSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PARTICIPACION_PROCESO',

			'id_name'		=> 'RES_ID',

			'fields'		=> ['RES_ID', 'PRO_ID', 'USU_ID', 'PAR_COMENTARIO', 'PAR_FECHA_HORA', 'PAR_DOCUMENTO', 'RES_DATOS', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'RES_ID' => 'INT',
				'PRO_ID' => 'INT',
				'USU_ID' => 'INT',
				'PAR_COMENTARIO' => 'STR',
				'PAR_FECHA_HORA' => 'STR',
				'PAR_DOCUMENTO' => 'INT',
				'RES_DATOS' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['RES_ID'],

			'autoincrement' => 'RES_ID',

			'nullable'		=> ['RES_ID', 'RES_DATOS', 'created_at', 'updated_at'],

			'required'		=> ['PRO_ID', 'USU_ID', 'PAR_COMENTARIO', 'PAR_FECHA_HORA', 'PAR_DOCUMENTO'],

			'uniques'		=> [],

			'rules' 		=> [
				'RES_ID' => ['type' => 'int'],
				'PRO_ID' => ['type' => 'int', 'required' => true],
				'USU_ID' => ['type' => 'int', 'required' => true],
				'PAR_COMENTARIO' => ['type' => 'str', 'required' => true],
				'PAR_FECHA_HORA' => ['type' => 'datetime', 'required' => true],
				'PAR_DOCUMENTO' => ['type' => 'int', 'required' => true],
				'RES_DATOS' => ['type' => 'str'],
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


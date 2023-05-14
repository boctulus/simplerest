<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_RECUPERARCONTRASENASchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_RECUPERARCONTRASENA',

			'id_name'		=> 'REC_ID',

			'fields'		=> ['REC_ID', 'REC_CORREO', 'REC_CODIGO', 'TBL_RECUPERARCONTRASENAcol', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'REC_ID' => 'INT',
				'REC_CORREO' => 'STR',
				'REC_CODIGO' => 'STR',
				'TBL_RECUPERARCONTRASENAcol' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['REC_ID'],

			'autoincrement' => 'REC_ID',

			'nullable'		=> ['REC_ID', 'REC_CORREO', 'REC_CODIGO', 'TBL_RECUPERARCONTRASENAcol', 'created_at', 'updated_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'REC_ID' => ['type' => 'int'],
				'REC_CORREO' => ['type' => 'str', 'max' => 255],
				'REC_CODIGO' => ['type' => 'str', 'max' => 10],
				'TBL_RECUPERARCONTRASENAcol' => ['type' => 'str', 'max' => 45],
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


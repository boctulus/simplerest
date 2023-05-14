<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_INFORMACION_CONTACTOSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_INFORMACION_CONTACTO',

			'id_name'		=> 'INF_ID',

			'fields'		=> ['INF_ID', 'INF_NOMBRE', 'INF_NUMERO', 'INF_SEDE', 'INF_CORREO', 'INF_BORRADO'],

			'attr_types'	=> [
				'INF_ID' => 'INT',
				'INF_NOMBRE' => 'STR',
				'INF_NUMERO' => 'STR',
				'INF_SEDE' => 'STR',
				'INF_CORREO' => 'STR',
				'INF_BORRADO' => 'INT'
			],

			'primary'		=> ['INF_ID'],

			'autoincrement' => 'INF_ID',

			'nullable'		=> ['INF_ID', 'INF_NOMBRE', 'INF_NUMERO', 'INF_SEDE', 'INF_CORREO', 'INF_BORRADO'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'INF_ID' => ['type' => 'int'],
				'INF_NOMBRE' => ['type' => 'str', 'max' => 255],
				'INF_NUMERO' => ['type' => 'str', 'max' => 255],
				'INF_SEDE' => ['type' => 'str', 'max' => 255],
				'INF_CORREO' => ['type' => 'str', 'max' => 255],
				'INF_BORRADO' => ['type' => 'bool']
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


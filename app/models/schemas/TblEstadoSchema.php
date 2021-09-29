<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEstadoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_estado',

			'id_name'		=> 'est_intId',

			'attr_types'	=> [
				'est_intId' => 'INT',
				'est_varNombre' => 'STR',
				'est_varIcono' => 'STR',
				'est_varColor' => 'STR',
				'est_dtimFechaCreacion' => 'STR',
				'est_dtimFechaActualizacion' => 'STR'
			],

			'nullable'		=> ['est_intId', 'est_dtimFechaCreacion', 'est_dtimFechaActualizacion'],

			'rules' 		=> [
				'est_varNombre' => ['max' => 20],
				'est_varIcono' => ['max' => 100],
				'est_varColor' => ['max' => 100]
			],

			'relationships' => [
				
			]
		];
	}	
}


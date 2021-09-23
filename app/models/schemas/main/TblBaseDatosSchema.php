<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblBaseDatosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_base_datos',

			'id_name'		=> 'dba_intId',

			'attr_types'	=> [
				'dba_intId' => 'INT',
				'dba_varNombre' => 'STR',
				'dba_dtimFechaCreacion' => 'STR',
				'dba_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['dba_intId', 'dba_dtimFechaCreacion', 'dba_dtimFechaActualizacion'],

			'rules' 		=> [
				'dba_varNombre' => ['max' => 250]
			],

			'relationships' => [
				
			]
		];
	}	
}


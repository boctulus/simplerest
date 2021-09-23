<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblScritpTablasSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_scritp_tablas',

			'id_name'		=> 'scr_intId',

			'attr_types'	=> [
				'scr_intId' => 'INT',
				'scr_varModulo' => 'STR',
				'scr_intOrden' => 'INT',
				'scr_varNombre' => 'STR',
				'scr_lonScritp' => 'STR'
			],

			'nullable'		=> ['scr_intId', 'scr_intOrden', 'scr_varNombre', 'scr_lonScritp'],

			'rules' 		=> [
				'scr_varModulo' => ['max' => 100],
				'scr_varNombre' => ['max' => 50]
			],

			'relationships' => [
				
			]
		];
	}	
}


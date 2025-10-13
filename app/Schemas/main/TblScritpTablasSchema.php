<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

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

			'primary'		=> ['scr_intId'],

			'autoincrement' => 'scr_intId',

			'nullable'		=> ['scr_intId', 'scr_varModulo', 'scr_intOrden', 'scr_varNombre', 'scr_lonScritp'],

			'uniques'		=> [],

			'rules' 		=> [
				'scr_intId' => ['type' => 'int'],
				'scr_varModulo' => ['type' => 'str', 'max' => 100],
				'scr_intOrden' => ['type' => 'int'],
				'scr_varNombre' => ['type' => 'str', 'max' => 50],
				'scr_lonScritp' => ['type' => 'str']
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


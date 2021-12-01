<?php

namespace simplerest\schemas\main;

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

			'primary'		=> ['dba_intId'],

			'autoincrement' => 'dba_intId',

			'nullable'		=> ['dba_intId', 'dba_dtimFechaCreacion', 'dba_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'dba_intId' => ['type' => 'int'],
				'dba_varNombre' => ['type' => 'str', 'max' => 250, 'required' => true],
				'dba_dtimFechaCreacion' => ['type' => 'datetime'],
				'dba_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'bool'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
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


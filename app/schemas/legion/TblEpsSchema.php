<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEpsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_eps',

			'id_name'		=> 'eps_intId',

			'attr_types'	=> [
				'eps_intId' => 'INT',
				'eps_varCodigo' => 'STR',
				'eps_varNombre' => 'STR',
				'eps_dtimFechaCreacion' => 'STR',
				'eps_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['eps_intId'],

			'autoincrement' => 'eps_intId',

			'nullable'		=> ['eps_intId', 'eps_dtimFechaCreacion', 'eps_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'eps_intId' => ['type' => 'int'],
				'eps_varCodigo' => ['type' => 'str', 'max' => 100, 'required' => true],
				'eps_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'eps_dtimFechaCreacion' => ['type' => 'datetime'],
				'eps_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
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

